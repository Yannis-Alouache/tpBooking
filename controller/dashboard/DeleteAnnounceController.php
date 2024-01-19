<?php

class DeleteAnnounceController extends Controller
{
    private const ROUTES = array(
        "@POST" => "deleteAnnounce",
    );

    public function getInnerRoutes(): array
	{
		return DeleteAnnounceController::ROUTES;
	}

    public function deleteAnnounce() {
        $announces = new AnnouncesModel();
        $announces->deleteAnnounce($_POST["announceId"]);

        if (isset($_SERVER["HTTP_REFERER"])) {
            header("Location: " . $_SERVER["HTTP_REFERER"]);
        }
    }

    public function render() {}
}