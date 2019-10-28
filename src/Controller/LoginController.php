<?php

namespace App\Controller;

use App\Repository\UsuarioRepository;
use Firebase\JWT\JWT;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class LoginController extends AbstractController
{
    /**
     * @var UsuarioRepository
     */
    private $repository;
    /**
     * @var UserPasswordEncoderInterface
     */
    private $encoder;

    public function __construct(
        UsuarioRepository $repository,
        UserPasswordEncoderInterface $encoder
    ) {
        $this->repository = $repository;
        $this->encoder = $encoder;
    }

    /**
     * @Route("/login", name="login")
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request)
    {
        $dataJson = json_decode($request->getContent($request));
        if (is_null($dataJson->usuario) || is_null($dataJson->senha)) {
            return new JsonResponse([
               'erro' => 'Favor enviar usuário e senha'
            ], Response::HTTP_BAD_REQUEST);
        }

        $user = $this->repository->findOneBy(['username' => $dataJson->usuario]);

        if (!$this->encoder->isPasswordValid($user, $dataJson->senha)) {
            return new JsonResponse([
                'erro' => 'Usuário ou senha inválidos'
            ], Response::HTTP_UNAUTHORIZED);
        }

        $token = JWT::encode(['username' => $user->getUsername()], 'chave', 'HS256');

        return new JsonResponse([
           'access_token' => $token
        ]);
    }
}
