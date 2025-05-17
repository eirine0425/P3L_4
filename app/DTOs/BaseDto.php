<?php

namespace App\DTOs;

abstract class BaseDto {
    public function only(array $fields): array {
        return array_intersect_key($this->toArray(), array_flip($fields));
    }

    public function except(array $fields): array {
        return array_diff_key($this->toArray(), array_flip($fields));
    }

    abstract public function toArray(): array;
}