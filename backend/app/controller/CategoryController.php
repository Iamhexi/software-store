<?php
require_once __DIR__ . '/../model/repository/CategoryRepository.php';
require_once __DIR__ . '/Controller.php';

class CategoryController implements Controller {
    private CategoryRepository $category_repository;

    public function __construct(CategoryRepository $category_repository = null) {
        $this->category_repository = $category_repository ?? new CategoryRepository;
    }

    public function get(Request $request): Response {
        $category_id = $request->get_path_parameter('category_id') ?? null;

        $category_name = $request->get_query_parameter('name') ?? null;
        $description = $request->get_query_parameter('description') ?? null;

        if ($category_id !== null) {
            $category = $this->category_repository->find($category_id);
            if ($category === null)
                return new Response(404, 'failure', null);
            return new Response(200, 'success', $category);
        }

        if ($category_name !== null) {
            $category = $this->category_repository->find_by('category_name', $category_name);
            if ($category === null)
                return new Response(404, 'failure', 'Category not found');
            return new Response(200, 'success', $category);
        } else if ($description !== null) {
            $category = $this->category_repository->find_by('description', $description);
            if ($category === null)
                return new Response(404, 'failure', 'Category not found');
            return new Response(200, 'success', $category);
        } else {
            $categories = $this->category_repository->find_all();
            if ($categories === null)
                return new Response(404, 'failure', 'Categories not found');
            return new Response(200, 'success', $categories);
        }

        $categories = $this->category_repository->find_all();
        return new Response(200, 'success', $categories);
    }

    public function post(Request $request): Response {
        $category_name = $request->get_body_parameter('name') ?? null;

        if ($category_name === null)
            return new Response(400, 'failure', null);

        $category = $this->category_repository->find_by('category_name', $category_name);
        if ($category !== null)
            return new Response(400, 'failure', null);

        $category = new Category(null, $category_name);
        $category = $this->category_repository->save($category);
        return new Response(201, 'success', $category);
    }

    public function put(Request $request): Response {
        $category_id = $request->get_path_parameter('category_id') ?? null;
        $category_name = $request->get_query_parameter('name') ?? null;

        if ($category_id === null)
            return new Response(400, 'failure', null);

        if ($category_name === null)
            return new Response(400, 'failure', null);

        $category = $this->category_repository->find($category_id);
        if ($category === null)
            return new Response(404, 'failure', null);

    }
}