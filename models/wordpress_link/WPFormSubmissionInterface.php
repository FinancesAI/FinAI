<?php

namespace app\models\wordpress_link;

interface WPFormSubmissionInterface
{

    /**
     * @return string The name of the form
     */
    public function getFormName(): string;

    /**
     * @return bool Whether the form submission has been synced with the CRM
     */
    public function getIsSynced(): bool;
}