<?php

declare(strict_types=1);

namespace Vkbd\Observer;

use React\MySQL\ConnectionInterface;
use React\Promise\PromiseInterface;
use Vkbd\Vk\NumericVkId;
use Vkbd\Person\FullName;
use React\MySQL\QueryResult;

use function React\Promise\reject;
use function React\Promise\resolve;

final class ObserverStorage
{
    private ConnectionInterface $connection;

    public function __construct(ConnectionInterface $connection)
    {
        $this->connection = $connection;
    }

    public function findByVkId(NumericVkId $vkId): PromiseInterface
    {
        $columns = 'id, first_name, last_name, vk_id, should_always_be_notified';

        return $this->connection
            ->query(
                "SELECT $columns FROM observers WHERE vk_id = ?",
                [$vkId->id()]
            )
            ->then(static function (QueryResult $result) use ($vkId) {
                if (empty($result->resultRows)) {
                    throw ObserverWasNotFound::withVkId($vkId);
                }

                /** @var array<string, mixed> $row */
                $row = $result->resultRows[0];

                return new Observer(
                    new ObserverId((int) $row['id']),
                    new NumericVkId((int) $row['vk_id']),
                    new FullName((string) $row['first_name'], (string) $row['last_name']),
                    (bool) $row['should_always_be_notified'],
                );
            });
    }

    public function create(NumericVkId $vkId, FullName $fullName, bool $shouldAlwaysBeNotified = true): PromiseInterface
    {
        return $this
            ->observerDoesNotExist($vkId)
            ->then(fn () => $this->connection->query(
                'INSERT INTO observers (first_name, last_name, vk_id, should_always_be_notified) VALUES (?, ?, ?, ?)',
                [$fullName->firstName(), $fullName->lastName(), $vkId->id(), $shouldAlwaysBeNotified],
            ));
    }

    private function observerDoesNotExist(NumericVkId $vkId): PromiseInterface
    {
        return $this->connection
            ->query(
                'SELECT 1 FROM observers WHERE vk_id = ?',
                [$vkId->id()]
            )
            ->then(
                fn (QueryResult $result): PromiseInterface => empty($result->resultRows) ?
                    resolve() :
                    reject(new ObserverAlreadyExists())
            );
    }
}
