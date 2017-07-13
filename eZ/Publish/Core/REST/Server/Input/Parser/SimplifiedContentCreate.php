<?php

namespace eZ\Publish\Core\REST\Server\Input\Parser;

use eZ\Publish\API\Repository\ContentService;
use eZ\Publish\API\Repository\ContentTypeService;
use eZ\Publish\Core\REST\Common\Exceptions;
use eZ\Publish\Core\REST\Common\Input\BaseParser;
use eZ\Publish\Core\REST\Common\Input\FieldTypeParser;
use eZ\Publish\Core\REST\Common\Input\ParserTools;
use eZ\Publish\Core\REST\Common\Input\ParsingDispatcher;
use eZ\Publish\Core\REST\Server\ResourceResolver;
use Symfony\Component\HttpFoundation\Request;

class SimplifiedContentCreate extends BaseParser
{
    const REST_API_VERSION = 2;

    /**
     * Content service.
     *
     * @var \eZ\Publish\API\Repository\ContentService
     */
    protected $contentService;

    /**
     * ContentType service.
     *
     * @var \eZ\Publish\API\Repository\ContentTypeService
     */
    protected $contentTypeService;

    /**
     * FieldType parser.
     *
     * @var \eZ\Publish\Core\REST\Common\Input\FieldTypeParser
     */
    protected $fieldTypeParser;

    /**
     * LocationCreate parser.
     *
     * @var \eZ\Publish\Core\REST\Server\Input\Parser\LocationCreate
     */
    protected $locationCreateParser;

    /**
     * Parser tools.
     *
     * @var \eZ\Publish\Core\REST\Common\Input\ParserTools
     */
    protected $parserTools;

    /**
     * Resource resolver.
     *
     * @var \eZ\Publish\Core\REST\Server\ResourceResolver
     */
    protected $resourceResolver;

    /**
     * Construct.
     *
     * @param \eZ\Publish\API\Repository\ContentService $contentService
     * @param \eZ\Publish\API\Repository\ContentTypeService $contentTypeService
     * @param \eZ\Publish\Core\REST\Common\Input\FieldTypeParser $fieldTypeParser
     * @param \eZ\Publish\Core\REST\Server\Input\Parser\LocationCreate $locationCreateParser
     * @param \eZ\Publish\Core\REST\Common\Input\ParserTools $parserTools
     * @param \eZ\Publish\Core\REST\Server\ResourceResolver $resourceResolver
     */
    public function __construct(
        ContentService $contentService,
        ContentTypeService $contentTypeService,
        FieldTypeParser $fieldTypeParser,
        LocationCreate $locationCreateParser,
        ParserTools $parserTools,
        ResourceResolver $resourceResolver
    ) {
        $this->contentService = $contentService;
        $this->contentTypeService = $contentTypeService;
        $this->fieldTypeParser = $fieldTypeParser;
        $this->locationCreateParser = $locationCreateParser;
        $this->parserTools = $parserTools;
        $this->resourceResolver = $resourceResolver;
    }

    /**
     * Parse input structure.
     *
     * @param array $data
     * @param \eZ\Publish\Core\REST\Common\Input\ParsingDispatcher $parsingDispatcher
     *
     * @return \eZ\Publish\API\Repository\Values\ValueObject
     */
    public function parse(array $data, ParsingDispatcher $parsingDispatcher)
    {
        if (!array_key_exists('ContentType', $data) || !is_string($data['ContentType'])) {
            throw new Exceptions\Parser("Missing or invalid 'ContentType' element for ContentCreate.");
        }

        $contentType = $this->resourceResolver->resolve($data['ContentType']);

        if (!array_key_exists('ContentSection', $data) || !is_string($data['ContentSection'])) {
            throw new Exceptions\Parser("Missing or invalid 'ContentSection' element for ContentCreate.");
        }

        $section = $this->resourceResolver->resolve($data['ContentSection']);

        if (!array_key_exists('Owner', $data) || !is_string($data['Owner'])) {
            throw new Exceptions\Parser("Missing or invalid 'Owner' element for ContentCreate.");
        }

        $owner = $this->resourceResolver->resolve($data['Owner']);

        $contentCreate = $this->contentService->newContentCreateStruct($contentType, 'eng-US');


        die('---');


        /**
         *


        // TODO: Retrieve default language code
        'eng-US'
        );


        $contentCreate->sectionId = $section->id;
        $contentCreate->ownerId = $owner->id;

        $contentCreate->alwaysAvailable = (bool) $input['alwaysAvailable'];
        $contentCreate->remoteId = $input['remoteId'];
        $contentCreate->modificationDate = new \DateTime($input['modificationDate']);

        foreach ($input['fields'] as $languageCode => $fields) {
            foreach ($fields as $fieldDefIdentifier => $plainValue) {
                $contentCreate->setField($fieldDefIdentifier, $plainValue, $languageCode);
            }
        }

        return $contentCreate;
         */
    }

    public function getRestHref($href)
    {
        return sprintf('/api/ezp/v%d%s',self::REST_API_VERSION, $href);
    }
}