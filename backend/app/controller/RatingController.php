<?php
require_once __DIR__.'/Controller.php';
require_once __DIR__.'/../model/repository/RatingRepository.php';

// `api/software/{software_id}/rating/average`
class RatingController extends Controller {
    private Repository $rating_repository;

    public function __construct(Repository $rating_repository = new RatingRepository) {
        $this->rating_repository = $rating_repository;
    }

    public function get(Request $request): Response {
        $software_id = $request->get_path_parameter(1);

        if ($software_id === null)
            return new Response(400, 'failure', 'Could not find rating with the given software id ' . $software_id);
        else if ($request->get_path_parameter(2) === 'average')
            return new Response(200, 'success', ['average' => $this->rating_repository->get_average($software_id)]);
        else if ($request->get_path_parameter(2) === 'count')
            return new Response(200, 'success', ['count' => $this->rating_repository->get_count($software_id)]);

        $rating = $this->rating_repository->find_by('software_id', $software_id);
        if ($rating === null)
            return new Response(404, 'failure', 'Could not find any rating with the given software id ' . $software_id);
        return new Response(200, 'success', ['rating' => $rating]);
    }

    public function post(Request $request): Response {
        $software_id = $request->get_path_parameter(1);
        $user_id = $request->get_body_parameter('user_id');
        $mark = $request->get_body_parameter('mark');

        if ($software_id === null)
            return new Response(400, 'failure', 'Cannot insert a rating without a software id');
        else if ($user_id === null)
            return new Response(400, 'failure', 'Cannot insert a rating without a user id');
        else if ($mark === null)
            return new Response(400, 'failure', 'Cannot insert a rating without a mark');
        else if (!is_numeric($mark) || $mark < 0 || $mark > 5)
            return new Response(400, 'failure', 'Cannot insert a rating with a mark that is not a number between 0 and 5');

        $result = $this->rating_repository->save(new Rating(
            rating_id: null,
            author_id: $user_id,
            software_id: $software_id,
            mark: $mark,
            date_added: new DateTime()
        ));

        return $result ? 
            new Response(201, 'success', 'Successfully inserted a rating') :
            new Response(500, 'failure', 'Could not insert a rating');
    }

    public function put(Request $request): Response {
        return new Response(405, 'failure', 'GET method not allowed');
    }

    public function delete(Request $request): Response {
        return new Response(405, 'failure', 'DELETE method not allowed');
    }
}