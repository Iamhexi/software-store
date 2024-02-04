<?php
require_once __DIR__ . '/../model/repository/CategoryRepository.php';
require_once __DIR__ . '/../model/Category.php';
require_once __DIR__ . '/Controller.php';

class CategoryController extends Controller {
    private CategoryRepository $category_repository;

    public function __construct(CategoryRepository $category_repository = null) {
        $this->category_repository = $category_repository ?? new CategoryRepository;
    }

    public function get(Request $request): Response {
        $category_id = $request->get_path_parameter(1); 

        $category_name = $request->get_query_parameter('name');
        $description = $request->get_query_parameter('description');

        if ($category_id !== null && is_numeric($category_id)) {
            $category = $this->category_repository->find($category_id);
            if ($category === null)
                return new Response(404, 'failure', 'Could not find category with the given id ' . $category_id);
            return new Response(200, 'success', $category);
        }

        if ($category_name !== null) {
            $category = $this->category_repository->find_by(['name' => $category_name]);
            if ($category === null)
                return new Response(404, 'failure', 'Category not found');
            return new Response(200, 'success', $category);
        } else if ($description !== null) {
            $category = $this->category_repository->find_by(['description' => $description]);
            if ($category === null)
                return new Response(404, 'failure', 'Category not found');
            return new Response(200, 'success', $category);
        } else {
            $categories = $this->category_repository->find_all();
            if ($categories === [])
                return new Response(404, 'failure', 'There are no categories available');
            return new Response(200, 'success', $categories);
        }

        return new Response(400, 'failure', 'Could not find category with the given id ' . $category_id);
    }

    public function post(Request $request): Response {
        $category_name = $request->get_body_parameter('name');
        $description = $request->get_body_parameter('description');

        if ($category_name === null)
            return new Response(400, 'failure', 'Cannot insert a category without a name');
        else if ($description === null)
            return new Response(400, 'failure', 'Cannot insert a category without a description');

        $categories = $this->category_repository->find_by(['name' => $category_name]);
        if ($categories === [])
            return new Response(400, 'failure', 'Cannot insert a category with the same name as already existing one.');

        $category = new Category(null, $category_name, $description);
        $result = $this->category_repository->save($category);
        if ($result === true)
            return new Response(201, 'success', $category);
        else
            return new Response(500, 'failure', 'Could not create new category due to an internal error');
    }

    public function put(Request $request): Response {
        $category_id = $request->get_path_parameter(1);
        $category_name = $request->get_body_parameter('name');
        $description = $request->get_body_parameter('description');

        if ($category_id === null)
            return new Response(400, 'failure', 'Cannot without an id');

        if ($category_name === null && $description === null)
            return new Response(400, 'failure', 'No data to update. Send either name or description or both');

        $category = $this->category_repository->find($category_id);
        if ($category === null)
            return new Response(404, 'failure', 'Cannot update non-existing category');

        if ($category_name !== null) {
            $category->name = $category_name;
        }

        if ($description !== null) {
            $category->description = $description;
        }

        $status = $this->category_repository->save($category);
        if ($status === false)
            return new Response(500, 'failure', 'Could not update category due to an internal error');
        return new Response(200, 'success', 'Category updated with id ' . $category_id);

    }

    public function delete(Request $request): Response {
        $category_id = $request->get_path_parameter(1);

        if ($category_id === null)
            return new Response(400, 'failure', 'Cannot delete category without an id');

        $category = $this->category_repository->find($category_id);
        if ($category === null)
            return new Response(404, 'failure', 'Category not found. Cannot delete non-existing category');

        if (!$this->category_repository->delete($category_id))
            return new Response(500, 'failure', 'Could not delete category due to an internal error');
        
        return new Response(200, 'success', 'Category deleted with id ' . $category_id);
    }
}