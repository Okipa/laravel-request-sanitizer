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
     *
     * @property boolean $sanitizeEntries
     */
    protected $sanitizeEntries = true;
    /**
     * Except the given keys (dot notation accepted) from the request entries sanitizing.
     * It can be a good option when you have numbers beginning with a zero that you want to keep that way, for example.
     *
     * @property array $exceptFromSanitize
     */
    protected $exceptFromSanitize = [];
    /**
     * Recursively exclude all the null entries from the request.
     * Declare this property to false to disable the null entries exclusion.
     *
     * @property boolean $excludeNullEntries
     */
    protected $excludeNullEntries = true;
    /**
     * Except the given keys (dot notation accepted) from the null entries exclusion.
     *
     * @property array $exceptFromNullExclusion
     */
    protected $exceptFromNullExclusion = [];
    /**
     * Set which request keys (dot notation accepted) should be safety checked, according to their types.
     * Use case : protected $safetyChecks = ['active' => 'boolean', 'permissions' => 'array'];.
     * Accepted types values : boolean / array.
     * The keys declared in this array will take the following values (according to their declared types) if they are
     * not found in the request :
     * - boolean : false
     * - array : []
     *
     * @property array $safetyChecks
     */
    protected $safetyChecks = [];

    /**
     * Get data to be validated from the request.
     *
     * @return array
     */
    protected function validationData(): array
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
        $this->replace($this->safetyCheckValues());
    }

    /**
     * Sanitize the request entries
     *
     * @return array
     */
    protected function sanitizeEntries(): array
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
    protected function excludeNullEntries(): array
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
     *
     * @return void
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
     * Check if the given values are present in the request and set their default value if not.
     *
     * @return array
     */
    protected function safetyCheckValues(): array
    {
        $toMerge = [];
        foreach ($this->safetyChecks as $key => $type) {
            $defaultValue = null;
            switch ($type) {
                case 'boolean':
                    $defaultValue = false;
                    break;
                case 'array':
                    $defaultValue = [];
                    break;
            }
            $value = $this->input($key, $defaultValue);
            array_set($toMerge, $key, $value);
        }
        $mergedBooleanValuesEntries = array_replace_recursive($this->all(), $toMerge);

        return $mergedBooleanValuesEntries;
    }
}
