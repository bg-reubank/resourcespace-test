<?php

namespace Captioning\Format;

use Captioning\File;

class WebvttFile extends File
{
    const TIMECODE_PATTERN = '#^((?:[0-9]{2}:)?[0-9]{2}:[0-9]{2}.[0-9]{3}) --> ((?:[0-9]{2}:)?[0-9]{2}:[0-9]{2}.[0-9]{3})( .*)?$#';

    protected $regions = array();

    public function parse()
    {
        $fileContentArray = $this->getFileContentAsArray();
        $parsing_errors = array();
        $i = 2;

        // Parse signature.
        if (rtrim($this->getNextValueFromArray($fileContentArray)) !== 'WEBVTT') {
            $parsing_errors[] = 'Missing "WEBVTT" at the beginning of the file';
        }

        // Parse regions.
        while (($line = $this->getNextValueFromArray($fileContentArray)) !== '') {
            if (strpos($line, 'Region:') === 0) {
                try {
                    $this->addRegion(WebvttRegion::parseFromString($line));
                } catch (\Exception $e) {
                    $parsing_errors[] = $e->getMessage();
                }
            } else {
                $parsing_errors[] = 'Incorrect Region definition at line ' . $i;
            }
            ++$i;
        }

        // Skip blank lines after signature.
        while ($line === '') {
            $line = $this->getNextValueFromArray($fileContentArray);
            ++$i;
        }

        $note = '';
        $id = '';

        // Parse cues (comments, ids if they exists).
        do {
            // Comment.
            if (strpos($line, 'NOTE') === 0) {

                if (trim($line) !== 'NOTE') {
                    $note = trim(ltrim($line, 'NOTE '));
                }
                $note .= $this->lineEnding;
                // Comment continues until there is a blank line.
                ++$i;
                while (trim($line = $this->getNextValueFromArray($fileContentArray)) !== '') {
                    $note .= $line.$this->lineEnding;
                    $i++;
                }
                continue;
            }

            // Timecode.
            $matches = array();
            $timecode_match = preg_match(self::TIMECODE_PATTERN, $line, $matches);

            if ($timecode_match) {
                $start = $matches[1];
                $stop = $matches[2];
                $settings = isset($matches[3]) ? trim($matches[3]): '';

                // Cue continues until there is a blank line.
                $text = '';
                ++$i;
                while (trim($line = $this->getNextValueFromArray($fileContentArray)) !== '') {
                    $text .= $line . $this->lineEnding;
                    ++$i;
                }

                // Make the cue object and add it to the file.
                $cue = $this->createCue($start, $stop, $text, $settings, $id, $note);
                $note = $id = '';
                $this->addCue($cue);
                unset($cue);
            } elseif ($line !== '') {
                // Supposse what not empty line before timeline is id.
                $id = $line;
            } else {
                $parsing_errors[] = 'Malformed cue detected at line ' . $i;
            }
            ++$i;
        } while (($line = $this->getNextValueFromArray($fileContentArray)) !== false);

        if (count($parsing_errors) > 0) {
            throw new \Exception('The following errors were found while parsing the file:'."\n".print_r($parsing_errors, true));
        }

        return $this;
    }

    public function addRegion(WebvttRegion $_region)
    {
        $this->regions[] = $_region;

        return $this;
    }

    public function getRegion($_index)
    {
        if (!isset($this->regions[$_index])) {
            return;
        }

        return $this->regions[$_index];
    }

    public function getRegions()
    {
        return $this->regions;
    }

    public function buildPart($_from, $_to)
    {
        $this->sortCues();

        $buffer = "WEBVTT".$this->lineEnding;

        foreach ($this->regions as $region) {
            $buffer .= $region.$this->lineEnding;
        }
        $buffer .= $this->lineEnding;

        if ($_from < 0 || $_from >= $this->getCuesCount()) {
            $_from = 0;
        }

        if ($_to < 0 || $_to >= $this->getCuesCount()) {
            $_to = $this->getCuesCount() - 1;
        }

        for ($j = $_from; $j <= $_to; $j++) {
            $buffer .= $this->getCue($j).$this->lineEnding;
        }

        $this->fileContent = $buffer;

        return $this;
    }

    /**
     * @param string $start
     * @param string $stop
     * @param string $text
     * @param string $settings
     * @param string $id
     * @param string $note
     * @return WebvttCue
     */
    private function createCue($start, $stop, $text, $settings, $id, $note)
    {
        $cue = new WebvttCue($start, $stop, $text);
        $tmp = explode(' ', trim($settings));
        foreach ($tmp as $setting) {
            $tmp2 = explode(':', $setting);

            if (count($tmp2) !== 2) {
                continue;
            }

            $cue->setSetting($tmp2[0], $tmp2[1]);
        }

        if ($id !== null) {
            $cue->setIdentifier($id);
        }
        if (!empty($note)) {
            $cue->setNote($note);
        }

        return $cue;
    }
}
