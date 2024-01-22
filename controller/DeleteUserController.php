<?php

class DeleteUserController extends Controller
{
    private const ROUTES = array(
        "@POST" => "deleteUser",
    );

    public function getInnerRoutes(): array
	{
		return DeleteUserController::ROUTES;
	}

    public function deleteUser() {
        $users = new UserModel();
        $users->deleteUser($_POST["userId"]);

        header("Location: " . '/user-list');
    }

    public function render() {}
}