<?php

namespace App\Tests\Api;

class UserApiTest extends ApiTestCase
{
    public function testRegistrationIsPublicAndHashesPassword(): void
    {
        $email = 'inscrit.'.uniqid().'@exemple.test';
        $this->jsonRequest('POST', '/api/users', ['email' => $email, 'name' => 'Inscrit', 'plainPassword' => 'secret123']);

        self::assertResponseStatusCodeSame(201);
        self::assertArrayNotHasKey('password', $this->responseData());
        self::assertArrayNotHasKey('plainPassword', $this->responseData());

        // le mot de passe hashé permet de se connecter
        $this->login($email, 'secret123');
    }

    public function testRegistrationRequiresPassword(): void
    {
        $this->jsonRequest('POST', '/api/users', ['email' => 'sans.mdp@exemple.test', 'name' => 'Sans']);

        self::assertResponseStatusCodeSame(422);
    }

    public function testCollectionIsAdminOnly(): void
    {
        $this->jsonRequest('GET', '/api/users', token: $this->login('user@goodpractice.test'));
        self::assertResponseStatusCodeSame(403);

        $this->jsonRequest('GET', '/api/users', token: $this->login('admin@goodpractice.test'));
        self::assertResponseIsSuccessful();
    }

    public function testUserCanReadSelfButNotOthers(): void
    {
        $adminToken = $this->login('admin@goodpractice.test');
        $this->jsonRequest('GET', '/api/users', token: $adminToken);
        $iris = [];
        foreach ($this->responseData()['member'] as $member) {
            $iris[$member['email']] = $member['@id'];
        }

        $userToken = $this->login('user@goodpractice.test');
        $this->jsonRequest('GET', $iris['user@goodpractice.test'], token: $userToken);
        self::assertResponseIsSuccessful();

        $this->jsonRequest('GET', $iris['editor@goodpractice.test'], token: $userToken);
        self::assertResponseStatusCodeSame(403);
    }

    public function testOnlyAdminCanPatchRoles(): void
    {
        $adminToken = $this->login('admin@goodpractice.test');
        $this->jsonRequest('GET', '/api/users', token: $adminToken);
        $userIri = null;
        foreach ($this->responseData()['member'] as $member) {
            if ('user@goodpractice.test' === $member['email']) {
                $userIri = $member['@id'];
            }
        }
        self::assertNotNull($userIri);

        $patch = fn (string $token) => $this->client->request('PATCH', $userIri, server: [
            'CONTENT_TYPE' => 'application/merge-patch+json',
            'HTTP_ACCEPT' => 'application/ld+json',
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
        ], content: json_encode(['roles' => ['ROLE_EDITOR']]));

        $patch($this->login('user@goodpractice.test'));
        self::assertResponseStatusCodeSame(403);

        $patch($adminToken);
        self::assertResponseIsSuccessful();
        self::assertContains('ROLE_EDITOR', $this->responseData()['roles']);
    }
}
