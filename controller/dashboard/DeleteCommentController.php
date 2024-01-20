<?php

class DeleteCommentController extends Controller
{
    private const ROUTES = array(
        "@POST" => "deleteComment",
    );

    public function getInnerRoutes(): array
	{
		return DeleteCommentController::ROUTES;
	}

    public function deleteComment() {
        $comments = new CommentModel();
        $comments->deleteComment($_POST["idAvis"]);

        if (isset($_SERVER["HTTP_REFERER"])) {
            header("Location: " . $_SERVER["HTTP_REFERER"]);
        }
    }

    public function render() {}
}