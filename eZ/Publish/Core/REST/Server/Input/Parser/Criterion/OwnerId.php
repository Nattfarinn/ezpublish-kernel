<?php

/**
 * File containing the LocationId Criterion parser class.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 *
 * @version //autogentag//
 */
namespace eZ\Publish\Core\REST\Server\Input\Parser\Criterion;

use eZ\Publish\Core\REST\Common\Input\BaseParser;
use eZ\Publish\Core\REST\Common\Input\ParsingDispatcher;
use eZ\Publish\Core\REST\Common\Exceptions;
use eZ\Publish\API\Repository\Values\Content\Query;

/**
 * Parser for LocationId Criterion.
 */
class OwnerId extends BaseParser
{
    /**
     * Parses input structure to a OwnerId Criterion object.
     *
     * @param array $data
     * @param \eZ\Publish\Core\REST\Common\Input\ParsingDispatcher $parsingDispatcher
     *
     * @throws \eZ\Publish\Core\REST\Common\Exceptions\Parser
     *
     * @return \eZ\Publish\API\Repository\Values\Content\Query\Criterion\OwnerId
     */
    public function parse(array $data, ParsingDispatcher $parsingDispatcher)
    {
        if (!array_key_exists('OwnerIdCriterion', $data)) {
            throw new Exceptions\Parser('Invalid <OwnerIdCriterion> format');
        }

        return new Query\Criterion\OwnerId(explode(',', $data['OwnerIdCriterion']));
    }
}
