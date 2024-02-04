<?php

trait JsonSerializableness {
    public function jsonSerialize(): mixed {
        return get_object_vars($this);
    }
}