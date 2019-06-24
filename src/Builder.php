<?php

namespace MilesChou\PhermUI;

use MilesChou\Pherm\Input\InputStream;
use MilesChou\Pherm\Output\OutputStream;
use MilesChou\Pherm\Terminal;

class Builder
{
    /**
     * @return PhermUI
     */
    public function build()
    {
        return new PhermUI(new Terminal(new InputStream, new OutputStream()));
    }
}
