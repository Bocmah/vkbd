<?php

declare(strict_types=1);

namespace Vkbd\Vk;

use Webmozart\Assert\Assert;

final class AlphanumericVkId implements VkId
{
    private string $id;

    public function __construct(string $id)
    {
        Assert::alnum($id, 'VK id must be an alphanumeric string');

        $this->id = $id;
    }

    public function id(): string
    {
        return $this->id;
    }
}
