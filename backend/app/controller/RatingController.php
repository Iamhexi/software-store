<?php
require_once __DIR__.'/Controller.php';
require_once __DIR__.'/../model/repository/RatingRepository.php';

// `api/software/{software_id}/rating/average`
class RatingController extends Controller {
    private RatingRepository $rating_repository;

    public function __construct(RatingRepository $rating_repository = new RatingRepository) {
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

        $rating = $this->rating_repository->find_by(['software_id' => $software_id]);
        if ($rating === null)
            return new Response(404, 'failure', 'Could not find any rating with the given software id ' . $software_id);
        return new Response(200, 'success', ['rating' => $rating]);
    }

    public function post(Request $request): Response {
        $software_id = $request->get_path_parameter(1);
        $user_id = $request->authority->user_id;
        $mark = $request->get_body_parameter('mark');

        if ($software_id === null)
            return new Response(400, 'failure', 'Cannot insert a rating without a software id');
        else if ($user_id === null)
            return new Response(400, 'failure', 'Cannot insert a rating without a user id');
        else if ($mark === null)
            return new Response(400, 'failure', 'Cannot insert a rating without a mark');
        else if (!is_numeric($mark) || $mark < 1 || $mark > 5)
            return new Response(400, 'failure', 'Cannot insert a rating with a mark that is not a number between 1 and 5');

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
        $software_id = $request->get_path_parameter(1);
        $mark = $request->get_body_parameter('mark');

        if ($software_id === null)
            return new Response(400, 'failure', 'No software_id given');

        $rating_id = $request->get_path_parameter(3);
        if ($rating_id === null)
            return new Response(400, 'failure', 'No raiting_id given');

        $rating = $this->rating_repository->find($rating_id);
        if ($rating === null)
            return new Response(404, 'failure', 'Could not find any rating with the given rating id');
        elseif ($rating->software_id !== $software_id)
            return new Response(404, 'failure', 'Rating doesn\'t match a software_id given');
        elseif ($rating->user_id !== $request->authority->user_id)
            return new Response(401, 'failure', 'Rating can\'t be updated by not owner of the rating');
        else if (!is_numeric($mark) || $mark < 1 || $mark > 5)
            return new Response(400, 'failure', 'Cannot insert a rating with a mark that is not a number between 1 and 5');
        
        if($this->rating_repository->update(new Rating($rating_id,-1,-1,$mark, new DateTime())))
            return new Response(200, 'success', 'Raiting has been updated');
        else
            return new Response(500, 'failure', 'Could not update a rating (?)');
    }

    public function delete(Request $request): Response {
        $software_id = $request->get_path_parameter(1);

        if ($software_id === null)
            return new Response(400, 'failure', 'No software_id given');

        $rating_id = $request->get_path_parameter(3);
        if ($rating_id === null)
            return new Response(400, 'failure', 'No raiting_id given');

        $rating = $this->rating_repository->find($rating_id);
        if ($rating === null)
            return new Response(404, 'failure', 'Could not find any rating with the given rating id');
        elseif ($rating->software_id !== $software_id)
            return new Response(404, 'failure', 'Rating doesn\'t match a software_id given');
        elseif ($rating->user_id !== $request->authority->user_id)
            return new Response(401, 'failure', 'Rating can\'t be deleted by not owner of the rating');
        else
        {
            if($this->rating_repository->delete($rating_id))
                return new Response(200, 'success', 'Raiting has been deleted');
            else
                return new Response(500, 'failure', 'Could not delete a rating (?)');
        }
    }
}