<?php

namespace App\Utils;

enum TypeProduct: string
{
    case Bazar = "Bazar";
    case Alimentacion = "Alimentacion";
    case Limpieza = "Limpieza";

    public function toString(): string
    {
        return $this->value;
    }
}
