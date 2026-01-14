<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

abstract class Controller
{
    /**
     * Standardized Success Response
     */
    public function sendResponse($data = null, string $message = 'Success', array $meta = [], int $code = Response::HTTP_OK): JsonResponse
    {
        $response = [
            'success' => true,
            'message' => $message,
            'data' => $data ?? [],
            'meta' => $meta,
        ];

        if ($data instanceof LengthAwarePaginator) {
            $response['meta'] = [
                'current_page' => $data->currentPage(),
                'last_page' => $data->lastPage(),
                'per_page' => $data->perPage(),
                'total' => $data->total(),
                ...$meta,
            ];
            $response['data'] = $data->items();
        }

        return response()->json($response, $code);
    }

    /**
     * Standardized Error Response
     */
    public function sendError(string $message, array $errors = [], int $code = Response::HTTP_UNPROCESSABLE_ENTITY): JsonResponse
    {
        $response = [
            'success' => false,
            'message' => $message,
            'data' => null,
            'meta' => [],
        ];

        // Detailed errors in development
        if (!App::environment('production')) {
            $response['errors'] = $errors;
        } else {
            $response['errors'] = [$message];
        }

        return response()->json($response, $code);
    }

    /**
     * Validation Helper
     */
    public function validateRequest(Request $request, array $rules, array $messages = []): array
    {
        $validator = Validator::make($request->all(), $rules, $messages);
        
        if ($validator->fails()) {
            throw ValidationException::withMessages($validator->errors()->toArray());
        }

        return $validator->validated();
    }

    /**
     * Common HTTP Status Helpers
     */
    public function created($data = null, string $message = 'Created successfully'): JsonResponse
    {
        return $this->sendResponse($data, $message, [], Response::HTTP_CREATED);
    }

    public function noContent(string $message = 'No content'): JsonResponse
    {
        return $this->sendResponse(null, $message, [], Response::HTTP_NO_CONTENT);
    }

    public function badRequest(string $message = 'Bad request', array $errors = []): JsonResponse
    {
        return $this->sendError($message, $errors, Response::HTTP_BAD_REQUEST);
    }

    public function notFound(string $message = 'Resource not found'): JsonResponse
    {
        return $this->sendError($message, [], Response::HTTP_NOT_FOUND);
    }

    public function unauthorized(string $message = 'Unauthorized'): JsonResponse
    {
        return $this->sendError($message, [], Response::HTTP_UNAUTHORIZED);
    }

    /**
     * Pagination Helper 
     */
    protected function paginatedResponse(
        LengthAwarePaginator $paginator,
        string $resourceClass,
        string $message = 'Success',
        array $extraMeta = []
    ): JsonResponse {
        $resource = $resourceClass::collection($paginator);
        $resourceArray = $resource->response()->getData(true);

        return $this->sendResponse(
            $resourceArray['data'] ?? [],
            $message,
            array_merge($resourceArray['meta'] ?? [], $extraMeta)
        );
    }

}
