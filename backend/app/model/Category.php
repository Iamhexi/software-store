<?php

class Category { // Data Transfer Object

    public function __construct(
        public ?int $category_id,
        public string $name,
        public string $description
    ) {}

    

}