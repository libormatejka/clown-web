<?php declare(strict_types = 1);

namespace App\Model\Auth;

use Clown\Core\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Nette\Security\AuthenticationException;
use Nette\Security\Authenticator;
use Nette\Security\IIdentity;
use Nette\Security\Passwords;
use Nette\Security\SimpleIdentity;

/**
 * Přihlášení e-mailem a heslem proti entitě Clown\Core\Entity\User.
 * Identita nese klíče, které očekávají core admin šablony (firstname, lastname).
 */
final class PasswordAuthenticator implements Authenticator
{

	private const STATUS_ACTIVE = 1;

	public function __construct(
		private EntityManagerInterface $entityManager,
		private Passwords $passwords,
	)
	{
	}

	public function authenticate(string $username, string $password): IIdentity
	{
		$user = $this->entityManager->getRepository(User::class)->findOneBy([
			'email' => $username,
			'status' => self::STATUS_ACTIVE,
		]);

		if (!$user instanceof User || !$this->passwords->verify($password, $user->getPassword())) {
			throw new AuthenticationException('Neplatné přihlašovací údaje.');
		}

		return new SimpleIdentity($user->getId(), [], [
			'id' => $user->getId(),
			'firstname' => $user->getFirstname(),
			'lastname' => $user->getLastname(),
			'email' => $user->getEmail(),
			'status' => $user->getStatus(),
		]);
	}

}
