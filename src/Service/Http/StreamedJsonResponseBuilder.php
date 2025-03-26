<?php

namespace App\Service\Http;

use InvalidArgumentException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedJsonResponse;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\AbstractObjectNormalizer;
use Symfony\Component\Serializer\SerializerInterface;

/** Построитель потоковых ответов в формате Json */
class StreamedJsonResponseBuilder
{
    /** Поле указывающее необходимость нормализации */
    private bool $enableNormalization = true;
    /** Контекст для нормализации */
    private array $normalizationContext = [];

    public function __construct(private readonly SerializerInterface $serializer) {
        $this->resetContext();
    }

    /**
     * Отключение нормализации
     *
     * @return $this
     */
    public final function disableNormalization(): self
    {
        $this->enableNormalization = false;
        return $this;
    }

    /**
     * Установка указанного контекста нормализации
     *
     * @param array $context Новые данные для контекста нормализации
     * @param bool $merge Нужно ли объединять с прошлым контекстом
     * @return $this
     */
    public function withNormalizationContext(array $context, bool $merge): self
    {
        $this->normalizationContext = $merge ? array_merge($this->normalizationContext, $context) : $context;
        return $this;
    }

    /**
     * Возвращение сформированного объекта StreamedJsonResponse на основе построенного контекста.
     *
     * @param mixed $data Данные
     * @param integer $status Статус ответа
     * @param array $headers Заголовки ответа
     *
     * @return StreamedJsonResponse Потоковый ответ в формате Json
     */
    public final function build(mixed $data, int $status, array $headers = []): StreamedJsonResponse
    {
        $data = $this->resolveData($data);

        // Если тип данных не подходит, то создаём функцию с генератором
        if (!is_iterable($data)) {
            $data = function () use ($data) {
                return $data;
            };
        }

        $response = new StreamedJsonResponse(
            data: $data,
            status: $status,
            headers: $headers
        );

        // Сброс контекста
        $this->resetContext();

        return $response;
    }

    /**
     * Ответ с HTTP кодом 200 - OK
     *
     * @param mixed $data Данные
     * @return StreamedJsonResponse Потоковый ответ в формате Json
     */
    public final function ok(mixed $data): StreamedJsonResponse
    {
        return $this->build($data, Response::HTTP_OK);
    }

    /**
     * Ответ с HTTP кодом 201 - CREATED
     *
     * @param mixed $data Данные
     * @return StreamedJsonResponse Потоковый ответ в формате Json
     */
    public final function created(mixed $data): StreamedJsonResponse
    {
        return $this->build($data, Response::HTTP_CREATED);
    }

    /**
     * Сброс контекста сериализации
     *
     * @return void
     */
    private function resetContext(): void
    {
        $this->enableNormalization = true;
        $this->normalizationContext = self::defaultNormalizationContext();
    }

    /**
     * Нормализация данных
     *
     * @param mixed $data Данные
     * @return mixed Нормализованные данные
     */
    private function resolveData(mixed $data): mixed
    {
        if ($this->enableNormalization) {
            $data = match (true) {
                is_array($data) => $this->serializer->normalize($data, 'array', $this->normalizationContext),
                is_object($data) => $this->serializer->normalize($data, 'object', $this->normalizationContext),
                default => throw new InvalidArgumentException(
                    message: "StreamedJsonResponse can only be created from an array or object."
                )
            };
        }

        return $data;
    }

    /**
     * Получение контекста нормализации по умолчанию
     *
     * @return array Контекст нормализации по умолчанию
     */
    public static function defaultNormalizationContext(): array
    {
        return [
            AbstractNormalizer::CIRCULAR_REFERENCE_HANDLER => function (object $object) {
                if (method_exists($object, 'getId')) {
                    return $object->getId();
                }
                throw new \LogicException("Нужно допилить обработку CIRCULAR_REFERENCE_HANDLER!");
            },
            AbstractObjectNormalizer::ENABLE_MAX_DEPTH => true,
            'json_encode_options' => JsonResponse::DEFAULT_ENCODING_OPTIONS | JSON_UNESCAPED_UNICODE
        ];
    }
}