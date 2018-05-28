<?php

namespace Okipa\LaravelRequestSanitizer;

use DataSanitizer;
use Illuminate\Foundation\Http\FormRequest;

class RequestSanitizer extends FormRequest
{
    /**
     * Recursively sanitize the request entries.
     * To check how data will be sanitized, check the used package : https://github.com/Okipa/php-data-sanitizer.
     * Declare this property to false to disable the request entries sanitizing.
     */
    protected $sanitizeEntries = true;
    /**
     * Except the given keys (dot notation accepted) from the request entries sanitizing.
     * It can be a good option when you have numbers beginning with a zero that you want to keep that way, for example.
     */
    protected $exceptFromSanitize = [];
    /**
     * Recursively exclude all the null entries from the request.
     * Declare this property to false to disable the null entries exclusion.
     */
    protected $excludeNullEntries = true;
    /**
     * Except the given keys (dot notation accepted) from the null entries exclusion.
     */
    protected $exceptFromNullExclusion = [];
    /**
     * Set the which request keys (dot notation accepted) should be safety checked.
     * If a given key is declared in this array and is not found in the request, it will take « false » for value.
     */
    protected $safetyCheckBooleanValues = [];

    /**
     * Get data to be validated from the request.
     *
     * @return array
     */
    protected function validationData()
    {
        $this->sanitizeRequest();

        return parent::validationData();
    }

    /**
     * Sanitize the request.
     *
     * @return void
     */
    public function sanitizeRequest()
    {
        if (method_exists($this, 'before')) {
            $this->before();
        }
        if ($this->sanitizeEntries) {
            $this->replace($this->sanitizeEntries());
        }
        if ($this->excludeNullEntries) {
            $this->replace($this->excludeNullEntries());
        }
        $this->replace($this->checkEntriesBooleanValues());
    }

    /**
     * Sanitize the request entries
     *
     * @return array
     */
    protected function sanitizeEntries()
    {
        $sanitizedEntries = DataSanitizer::sanitize($this->except($this->exceptFromSanitize));
        $mergedSanitizedEntries = array_replace_recursive($this->all(), $sanitizedEntries);

        return $mergedSanitizedEntries;
    }

    /**
     * Exclude null entries from the request
     *
     * @return array
     */
    protected function excludeNullEntries()
    {
        $filteredEntries = [];
        $this->excludeNullEntriesProcess($this->all(), $filteredEntries);

        return $filteredEntries;
    }

    /**
     * Recursively exclude null entries from the given array
     *
     * @param array $entries
     * @param array $filteredEntries
     */
    protected function excludeNullEntriesProcess(array $entries, array &$filteredEntries)
    {
        foreach ($entries as $key => $entry) {
            if (! is_null($entry) || in_array($key, $this->exceptFromNullExclusion)) {
                if (is_array($entry)) {
                    $arrayEntries = [];
                    $this->excludeNullEntriesProcess($entry, $arrayEntries);
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
        $toMerge = [];
        foreach ($this->safetyCheckBooleanValues as $booleanValueKey) {
            $value = $this->input($booleanValueKey, false);
            array_set($toMerge, $booleanValueKey, $value);
        }
        $mergedBooleanValuesEntries = array_replace_recursive($this->all(), $toMerge);

        return $mergedBooleanValuesEntries;
    }
}
