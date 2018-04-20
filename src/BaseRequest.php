<?php

namespace Okipa\LaravelBaseRequest;

use Illuminate\Foundation\Http\FormRequest;
use InputSanitizer;

class BaseRequest extends FormRequest
{
    protected $checkEntriesBooleanValue = [];
    protected $exceptEntriesFromSanitize = [];

    /**
     * Validate the input
     *
     * @param  \Illuminate\Validation\Factory $factory
     *
     * @return \Illuminate\Validation\Validator
     */
    public function validator($factory)
    {
        // we sanitize the inputs
        $this->sanitizeInput();
        // we execute the before treatment
        if (method_exists($this, 'before')) {
            $this->container->call([$this, 'before']);
        }

        return $factory->make(
            $this->all(),
            $this->container->call([$this, 'rules']),
            $this->messages()
        );
    }

    /**
     * Sanitize the input
     *
     * @return void
     */
    protected function sanitizeInput()
    {
        // we sanitize the request entries
        $this->merge(InputSanitizer::sanitize($this->except($this->exceptEntriesFromSanitize)));
        // we filter the null entries from the request
        $this->replace($this->filterEntries($this->all()));
        // we check the request entries boolean values
        $this->checkEntriesBooleanValues();
    }

    /**
     * @param $entries
     *
     * @return array
     */
    private function filterEntries(array $entries)
    {
        $filteredEntries = [];
        $this->filterEntriesProcess($entries, $filteredEntries);

        return $filteredEntries;
    }

    /**
     * @param array $entries
     * @param array $filteredEntries
     */
    private function filterEntriesProcess(array $entries, array &$filteredEntries)
    {
        foreach ($entries as $key => $entry) {
            if (! is_null($entry)) {
                if (is_array($entry)) {
                    $arrayEntries = [];
                    $this->filterEntriesProcess($entry, $arrayEntries);
                    $filteredEntries[$key] = $arrayEntries;
                } else {
                    $filteredEntries[$key] = $entry;
                }
            }
        }
    }

    /**
     * Check if the given boolean values are given and set them to false if this is not the case.
     */
    protected function checkEntriesBooleanValues()
    {
        foreach ($this->checkEntriesBooleanValue as $entryKey) {
            $this->merge([$entryKey => $this->get($entryKey, false)]);
        }
    }
}
