<?php

namespace Ingenerator\ApiEmulator;

class HandlerRequestContext
{

    public function __construct(
        public readonly HandlerDataRepository $data_repository
    )
    {
    }
}
