<?php

namespace app\models\wordpress_link;

interface WPFormInterface
{
    /**
     * @return int The ID of the form
     */
    public function getId(): int;

    /**
     * @return string The name of the form
     */
    public function getName(): string;

    /**
     * @return bool Whether the form is active for data sync
     */
    public function getIsActive(): bool;

    /**
     * @return bool Whether the form is published to users
     */
    public function getIsPublished(): bool;

    /**
     * @return string The title of the form
     */
    public function getStatusName(): string;
}