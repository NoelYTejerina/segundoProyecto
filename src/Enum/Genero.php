<?php

namespace App\Enum;

enum Genero: string
{
    case MASCULINO = 'masculino';
    case FEMENINO = 'femenino';
    case NO_BINARIO = 'no binario';
    case GENERO_FLUIDO = 'género fluido';
    case AGÉNERO = 'agénero';
    case BIGENERO = 'bigénero';
    case DEMIGENERO = 'demigénero';
    case TRANSFEMENINO = 'transfemenino';
    case TRANSMASCULINO = 'transmasculino';
    case INTERSEXUAL = 'intersexual';
    case DOS_ESPIRITUS = 'dos espíritus';
    case NEUTROIS = 'neutrois';
    case PANGENERO = 'pangénero';
    case OTRO = 'otro';

}
