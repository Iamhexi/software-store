<?php
require_once __DIR__ . '/../model/repository/ReviewRepository.php';
require_once __DIR__ . '/Controller.php';

class ReviewController extends Controller {
    private Repository $review_repository;

    public function __construct(Repository $review_repository = new ReviewRepository) {
        $this->review_repository = $review_repository;
    }

    public function get(Request $request): Response {
        $id = $request->get_path_parameter(1);
        $software_id = $request->get_query_parameter('software_id');
        
        if ($id !== null && is_numeric($id)) {
            $review = $this->review_repository->find($id);
            if ($review === null)
                return new Response(404, 'Failure', 'Review not found');
            else
                return new Response(200, 'Success', $review);
        } else if ($software_id !== null && is_numeric($software_id)) {
            return new Response(200, 'Success', $this->review_repository->find_by(['software_id' => $software_id]));
        } else {
            return new Response(200, 'Success', $this->review_repository->find_all());
        }
    }

    public function post(Request $request): Response {
        $title = $request->get_body_parameter('title');
        $author_id = $request->get_body_parameter('author_id');
        $software_id = $request->get_body_parameter('software_id');
        $description = $request->get_body_parameter('description');

        if ($title === null || $author_id === null || $software_id === null || $description === null)
            return new Response(400, 'Failure', 'Missing data');
        else {
            $review = new Review(
                review_id: null,
                author_id: $author_id,
                software_id: $software_id,
                title: $title,
                description: $description,
                date_added: new DateTime(),
                date_last_updated: new DateTime()
            );
            if ($this->review_repository->save($review))
                return new Response(200, 'Success', 'Review added');
            else
                return new Response(500, 'Failure', 'Internal server error');
        }
    }

    public function put(Request $request): Response {
        $id = $request->get_path_parameter(1);    
        $title = $request->get_body_parameter('title');
        $description = $request->get_body_parameter('description');

        if ($id === null && !is_numeric($id))
            return new Response(400, 'Failure', 'Cannot update review without id');

        if ($title === null || $description === null)
            return new Response(400, 'Failure', 'No data to update was provided. Add either title or description');
        
        $review = $this->review_repository->find($id);
        if ($review === null)
            return new Response(404, 'Failure', "Review with id $id has not been found");

        if ($title !== null)
            $review->title = $title;
        if ($description !== null)
            $review->description = $description;
        $review->date_last_updated = new DateTime();
        
        if ($this->review_repository->save($review))
            return new Response(200, 'Success', 'Review updated');
        else
            return new Response(500, 'Failure', 'Internal server error');
    }

    public function delete(Request $request): Response {
        $id = $request->get_path_parameter(1);
        
        if ($id === null && !is_numeric($id))
            return new Response(400, 'Failure', 'Cannot delete review without id');

        $review = $this->review_repository->find($id);
        if ($review === null)
            return new Response(404, 'Failure', "Review with id $id has not been found, thus, it cannot be deleted");

        if ($this->review_repository->delete($id))
            return new Response(200, 'Success', 'Review deleted');
        return new Response(500, 'Failure', 'Review could not be deleted due to an internal error (possibly database)');
    }
    


}