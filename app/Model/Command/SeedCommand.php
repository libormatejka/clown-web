<?php declare(strict_types = 1);

namespace App\Model\Command;

use App\Model\Service\MenuProvider;
use Clown\Core\Entity\Menu;
use Clown\Core\Entity\MenuItem;
use Clown\Core\Entity\Page;
use Clown\Core\Entity\User;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Nette\Security\Passwords;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:seed', description: 'Vytvoří admin uživatele, stránky O nás / O projektu a hlavní menu.')]
final class SeedCommand extends Command
{

	public function __construct(
		private EntityManagerInterface $entityManager,
		private Passwords $passwords,
	)
	{
		parent::__construct();
	}

	protected function configure(): void
	{
		$this->addOption('email', null, InputOption::VALUE_REQUIRED, 'E-mail admin uživatele', 'admin@clown-web.local');
		$this->addOption('password', null, InputOption::VALUE_REQUIRED, 'Heslo admin uživatele (bez volby se vygeneruje náhodné)');
	}

	protected function execute(InputInterface $input, OutputInterface $output): int
	{
		$email = (string) $input->getOption('email');
		$password = $input->getOption('password');

		$user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $email]);
		if (!$user instanceof User) {
			$plainPassword = is_string($password) && $password !== '' ? $password : bin2hex(random_bytes(8));

			$user = new User();
			$user->setEmail($email);
			$user->setFirstname('Admin');
			$user->setLastname('Clown');
			$user->setPhone(null);
			$user->setPassword($this->passwords->hash($plainPassword));
			$user->setStatus(1);
			$user->setCreatedAt(new DateTime());
			$user->setGoogleLoginEnabled(false);
			$user->setTwoFactorEnabled(false);
			$user->setTwoFactorAllowed(false);
			$this->entityManager->persist($user);

			$output->writeln(sprintf('Uživatel: %s / heslo: %s', $email, $plainPassword));
		} else {
			$output->writeln(sprintf('Uživatel %s už existuje, heslo zůstává beze změny.', $email));
		}

		$this->createPage(
			$user,
			'O nás',
			'o-nas',
			'Kdo za Clown Webem stojí.',
			'<p>Jsme malý tým, který staví přenositelné redakční systémy z opakovaně použitelných balíčků.</p><p>Tento web slouží jako důkaz, že se z balíčku <strong>clown/core</strong> dá postavit funkční web s administrací.</p>',
		);
		$this->createPage(
			$user,
			'O projektu',
			'o-projektu',
			'Co je Clown CMS.',
			'<p>Clown CMS se skládá z jádra (<strong>clown-core</strong>) a volitelných addonů. Web si jádro nainstaluje přes Composer a aktualizuje stejným způsobem.</p><p>Tato ukázka používá jen jádro: stránky, menu, uživatele a administraci.</p>',
		);

		$menu = $this->entityManager->getRepository(Menu::class)->findOneBy(['title' => MenuProvider::MAIN_MENU_TITLE]);
		if (!$menu instanceof Menu) {
			$menu = new Menu();
			$menu->setTitle(MenuProvider::MAIN_MENU_TITLE);
			$this->entityManager->persist($menu);

			$this->createMenuItem($menu, 'O nás', '/o-nas/', 1);
			$this->createMenuItem($menu, 'O projektu', '/o-projektu/', 2);
		}

		$this->entityManager->flush();
		$output->writeln('Hotovo.');

		return Command::SUCCESS;
	}

	private function createPage(User $user, string $title, string $slug, string $perex, string $content): void
	{
		if ($this->entityManager->getRepository(Page::class)->findOneBy(['slug' => $slug]) !== null) {
			return;
		}

		$page = new Page();
		$page->setTitle($title);
		$page->setSlug($slug);
		$page->setPerex($perex);
		$page->setContent($content);
		$page->setThumbnail(null);
		$page->setMetaTitle(null);
		$page->setMetaDescription($perex);
		$page->setMetaImage(null);
		$page->setMetaIndex(null);
		$page->setUser($user);
		$page->setStatus('publish');
		$page->setLayout('one-column');
		$page->setPublishedAt(new DateTime());
		$page->setUpdatedAt(null);
		$this->entityManager->persist($page);
	}

	private function createMenuItem(Menu $menu, string $title, string $slug, int $position): void
	{
		$item = new MenuItem();
		$item->setMenu($menu);
		$item->setTitle($title);
		$item->setSlug($slug);
		$item->setPosition($position);
		$item->setStatus('publish');
		$item->setTarget(null);
		$item->setDescription(null);
		$item->setIcon(null);
		$item->setParent(null);
		$item->setParentId(null);
		$this->entityManager->persist($item);
	}

}
