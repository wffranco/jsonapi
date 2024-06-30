<?php

namespace App\Console\Commands\Concerns;

use Illuminate\Console\Command;

/**
 * @property-read \Illuminate\Console\OutputStyle $output
 *
 * @mixin Command
 */
trait WriteInline
{
    public function inline($string, $style = null, $verbosity = null)
    {
        $styled = $style ? "<$style>$string</$style>" : $string;
        $this->output->write($styled, false, $this->parseVerbosity($verbosity));
    }

    /**
     * Write a string as information output.
     *
     * @return void
     */
    public function titled($title, $string, $verbosity = null)
    {
        $this->inline($title.': ', 'info', null);
        $this->line($string, null, $verbosity);
    }
}
