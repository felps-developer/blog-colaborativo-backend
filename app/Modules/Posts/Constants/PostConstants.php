<?php

namespace App\Modules\Posts\Constants;

class PostConstants
{
    /**
     * Pagination constants
     */
    public const DEFAULT_PAGE = 1;
    public const DEFAULT_PER_PAGE = 10;
    public const MAX_PER_PAGE = 100;
    public const PAGINATION_PARAM = 'page';

    /**
     * Validation constants
     */
    public const MAX_TITLE_LENGTH = 255;
    public const MIN_TITLE_LENGTH = 1;
}

