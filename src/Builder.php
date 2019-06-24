<?php

namespace MilesChou\PhermUI;

use MilesChou\Pherm\Input\InputStream;
use MilesChou\Pherm\Output\OutputStream;
use MilesChou\Pherm\Terminal;

class Builder
{
    /**
     * @return
     */
    public function build()
    {
        return new Cui(new Terminal(new InputStream, new OutputStream()));
    }
}
