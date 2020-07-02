<?php
namespace Api\Handler;

use Api\Entity\CollectionInterface;
use Api\Entity\EntityInterface;
use Api\Mapper\MapperInterface;
use LosMiddleware\ApiServer\Exception\MethodNotAllowedException;
use LosMiddleware\ApiServer\Exception\ValidationException;
use Mezzio\Helper\UrlHelper;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Laminas\Diactoros\Response\EmptyResponse;
use Laminas\Diactoros\Response\JsonResponse;
use Mezzio\Hal\HalResponseFactory;
use Mezzio\Hal\ResourceGenerator;
use Laminas\InputFilter\InputFilterAwareInterface;
use Mezzio\ProblemDetails\ProblemDetailsResponseFactory;

abstract class AbstractRestHandler implements RequestHandlerInterface
{
    const IDENTIFIER_NAME = 'id';

    protected $parsedData;

    /** @var ServerRequestInterface */
    protected $request;

    /**
     * @var UrlHelper
     */
    protected $urlHelper;

    protected $itemCountPerPage = 25;

    /** @var MapperInterface */
    protected $mapper;

    /** @var ResourceGenerator */
    protected $resourceGenerator;

    /** @var HalResponseFactory */
    protected $responseFactory;

    /** @var ProblemDetailsResponseFactory */
    protected $problemDetailsFactory;

    /**
     * PlanHandler constructor.
     * @param MapperInterface $mapper
     * @param ResourceGenerator $resourceGenerator
     * @param HalResponseFactory $responseFactory
     * @param ProblemDetailsResponseFactory $problemDetailsFactory
     */
    public function __construct(
        MapperInterface $mapper,
        ResourceGenerator $resourceGenerator,
        HalResponseFactory $responseFactory,
        ProblemDetailsResponseFactory $problemDetailsFactory
    ) {
        $this->mapper = $mapper;
        $this->resourceGenerator = $resourceGenerator;
        $this->responseFactory = $responseFactory;
        $this->problemDetailsFactory = $problemDetailsFactory;
    }

    /**
     * @param ServerRequestInterface $request
     * @return ResponseInterface|JsonResponse
     * phpcs:disable
     */
    public function handle(ServerRequestInterface $request) : ResponseInterface
    {
        $requestMethod = strtoupper($request->getMethod());
        $id = $request->getAttribute(static::IDENTIFIER_NAME);

        $this->request = $request;

        try {
            switch ($requestMethod) {
                case 'GET':
                    return isset($id)
                        ? $this->handleFetch($id)
                        : $this->handleFetchAll();
                case 'POST':
                    if (isset($id)) {
                        return $this->problemDetailsFactory->createResponse(
                            $request,
                            405,
                            'Invalid entity operation POST'
                        );
                    }
                    return $this->handlePost();
                case 'PUT':
                    return isset($id)
                        ? $this->handleUpdate($id)
                        : $this->handleUpdateList();
                case 'PATCH':
                    return isset($id)
                        ? $this->handlePatch($id)
                        : $this->handlePatchList();
                case 'DELETE':
                    return isset($id)
                        ? $this->handleDelete($id)
                        : $this->handleDeleteList();
                case 'HEAD':
                    return $this->head();
                case 'OPTIONS':
                    return $this->options();
                default:
                    throw MethodNotAllowedException::create();
            }
        } catch (MethodNotAllowedException $ex) {
            return $this->generateErrorResponse('Method not allowed', 405);
        } catch (ValidationException $ex) {
            return $this->generateErrorResponse('Unprocessable Entity', 422, ['messages' => $ex->getMessage()]);
        } catch (\Exception $ex) {
            return $this->generateErrorResponse($ex->getMessage(), $ex->getCode());
        }
    }
    // phpcs:enable

    /**
     * Call the InputFilter to filter and validate data
     *
     * @throws ValidationException
     * @return array
     */
    protected function validateBody() : array
    {
        $data = $this->request->getParsedBody();

        if (! is_array($data)) {
            $data = [];
        }

        $entity = $this->mapper->createEntity();

        if (! ($entity instanceof InputFilterAwareInterface)) {
            return $data;
        }

        if (strtoupper($this->request->getMethod()) == 'PATCH') {
            $entity->getInputFilter()->setValidationGroup(array_keys($data));
        }

        if (! $entity->getInputFilter()->setData($data)->isValid()) {
            throw ValidationException::fromMessages($entity->getInputFilter()->getMessages());
        }

        $values = $entity->getInputFilter()->getValues();

        $parsed = [];
        foreach ($values as $key => $value) {
            if (array_key_exists($key, $data)) {
                $parsed[$key] = $value;
            }
        }
        return $parsed;
    }

    /**
     * Generates a proper response based on the Entity ot Collection
     *
     * @param EntityInterface|CollectionInterface $entity
     * @param int $statusCode
     * @return ResponseInterface
     */
    protected function generateResponse($entity, int $statusCode = 200) : ResponseInterface
    {
        $response = $this->responseFactory->createResponse(
            $this->request,
            $this->resourceGenerator->fromObject($entity, $this->request)
        );
        return $response->withStatus($statusCode);
    }

    /**
     * @param string $message
     * @param int $statusCode
     * @param array $arrayMessage
     * @return ResponseInterface
     */
    protected function generateErrorResponse(
        string $message,
        int $statusCode = 500,
        array $arrayMessage = []
    ) : ResponseInterface {
        return $this->problemDetailsFactory->createResponse(
            $this->request,
            $statusCode,
            $message,
            '',
            '',
            $arrayMessage
        );
    }

    /**
     * @param string $id
     * @return ResponseInterface|JsonResponse
     */
    protected function handleFetch(string $id) : ResponseInterface
    {
        $entity = $this->fetch($id);
        if ($entity === null) {
            return $this->generateErrorResponse('Entity not found', 404);
        }
        return $this->generateResponse($entity);
    }

    /**
     * @return ResponseInterface
     */
    protected function handleFetchAll() : ResponseInterface
    {
        return $this->generateResponse($this->fetchAll());
    }

    /**
     * @return ResponseInterface
     * @throws ValidationException
     */
    protected function handlePost() : ResponseInterface
    {
        return $this->generateResponse($this->create($this->validateBody()));
    }

    /**
     * @param string $id
     * @return ResponseInterface
     * @throws ValidationException
     */
    protected function handleUpdate(string $id) : ResponseInterface
    {
        $entity = $this->update($id, $this->validateBody());
        if ($entity === null) {
            return $this->generateErrorResponse('Entity not found', 404);
        }
        return $this->generateResponse($entity);
    }

    /**
     * @return ResponseInterface
     * @throws MethodNotAllowedException
     * @throws ValidationException
     */
    protected function handleUpdateList() : ResponseInterface
    {
        return $this->generateResponse($this->updateList($this->validateBody()));
    }

    /**
     * @param string $id
     * @return ResponseInterface
     * @throws ValidationException
     */
    protected function handlePatch(string $id) : ResponseInterface
    {
        $entity = $this->patch($id, $this->validateBody());
        if ($entity == null) {
            return $this->generateErrorResponse('Entity not found', 404);
        }
        return $this->generateResponse($entity);
    }

    /**
     * @return ResponseInterface
     * @throws MethodNotAllowedException
     * @throws ValidationException
     */
    protected function handlePatchList() : ResponseInterface
    {
        return $this->generateResponse($this->patchList($this->validateBody()));
    }

    /**
     * @param string $id
     * @return ResponseInterface
     */
    protected function handleDelete(string $id) : ResponseInterface
    {
        $entity = $this->delete($id);
        if ($entity === null) {
            return $this->generateErrorResponse('Entity not found', 404);
        }
        return new EmptyResponse(204);
    }

    /**
     * @return ResponseInterface
     * @throws MethodNotAllowedException
     */
    protected function handleDeleteList() : ResponseInterface
    {
        $this->deleteList();
        return new JsonResponse(null, 204);
    }

    /**
     * @param string $id
     * @return EntityInterface
     */
    public function fetch(string $id) : ?EntityInterface
    {
        $queryDeleted = $this->request->getQueryParams()['deleted'] ?? '0';
        $withDeleted = ($queryDeleted !== 'true' && $queryDeleted !== '1');
        return $this->mapper->fetchById($id, $withDeleted);
    }

    /**
     * @param array $query
     * @return CollectionInterface
     */
    public function fetchAll($query = [], $options = []) : CollectionInterface
    {
        $withDeleted = $this->request->getQueryParams()['deleted'] ?? '0';
        if ($withDeleted !== 'true' && $withDeleted !== '1') {
            $query['deleted'] = ['$ne' => true];
        }
        return $this->mapper->fetchAllBy($query, (bool) $withDeleted, $options);
    }

    public function create(array $data) : EntityInterface
    {
        $entity = $this->mapper->createEntity();
        $entity->exchangeArray($data);
        $this->mapper->insert($entity);
        return $entity;
    }

    public function update(string $id, array $data) : ?EntityInterface
    {
        $entity = $this->mapper->fetchById($id);
        if ($entity === null) {
            return null;
        }
        $this->mapper->update($entity, $data);
        return $entity;
    }

    /**
     * @param array $data
     * @return CollectionInterface
     * @throws MethodNotAllowedException
     */
    public function updateList(array $data) : CollectionInterface
    {
        throw new MethodNotAllowedException('Method not allowed', 405);
    }

    public function delete(string $id)
    {
        $entity = $this->mapper->fetchById($id);
        if ($entity === null) {
            return null;
        }
        $this->mapper->delete($entity);
        return $entity;
    }

    /**
     * @throws MethodNotAllowedException
     */
    public function deleteList()
    {
        throw new MethodNotAllowedException('Method not allowed', 405);
    }

    /**
     * @throws MethodNotAllowedException
     */
    public function head()
    {
        throw new MethodNotAllowedException('Method not allowed', 405);
    }

    /**
     * @throws MethodNotAllowedException
     */
    public function options()
    {
        throw new MethodNotAllowedException('Method not allowed', 405);
    }

    public function patch(string $id, array $data) : EntityInterface
    {
        return $this->update($id, $data);
    }

    /**
     * @param array $data
     * @return CollectionInterface
     * @throws MethodNotAllowedException
     */
    public function patchList(array $data) : CollectionInterface
    {
        throw new MethodNotAllowedException('Method not allowed', 405);
    }
}
