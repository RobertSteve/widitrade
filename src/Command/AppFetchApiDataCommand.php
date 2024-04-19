<?php

namespace App\Command;

use App\Repository\BusinessRepository;
use App\Repository\ProductDetailRepository;
use App\Repository\ProductImageRepository;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Contracts\HttpClient\HttpClientInterface;

#[AsCommand(
    name: 'app:fetch-api-data',
    description: 'Este comando se encarga de obtener los datos de la API y guardarlos en la base de datos.',
)]
class AppFetchApiDataCommand extends Command
{
    public function __construct(
        private HttpClientInterface $httpClient,
        private EntityManagerInterface $entityManager,
        private ProductRepository $productRepository,
        private ProductDetailRepository $productDetailRepository,
        private ProductImageRepository $productImageRepository,
        private BusinessRepository $businessRepository,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $helper = $this->getHelper('question');
        $question = new ConfirmationQuestion('¿Estás seguro de que deseas ejecutar este comando? (y/N) ', false);

        if (!$helper->ask($input, $output, $question)) {
            $output->writeln('Comando cancelado.');
            return Command::FAILURE;
        }

        $output->writeln('Iniciando la ejecución del comando...');

        $response = $this->httpClient->request('GET', 'https://file.notion.so/f/f/5f4961f1-3ce8-4cbb-9a67-ec3baf081ee7/1ed10d1f-fd07-4cfd-9c23-35d3d765ffd5/amazon.json?id=6b00230f-b9c5-4cf0-97ee-3a954baa361b&table=block&spaceId=5f4961f1-3ce8-4cbb-9a67-ec3baf081ee7&expirationTimestamp=1713542400000&signature=bN2-XLA5-K1Ub-Dtsj6z4EzYlHkCptFniZBWgXJ9HpA&downloadName=amazon.json');
        $result = $response->toArray();

        foreach ($result['SearchResult']['Items'] as $item) {
            $business = $this
                ->businessRepository
                ->findOrCreateByName($item['ItemInfo']['ByLineInfo']['Brand']['DisplayValue']);

            $product = $this
                ->productRepository
                ->findOneBy(['code' => $item['ASIN']]);

            if (!$product) {
                $product = $this
                    ->productRepository
                    ->createByApi($item, $business);

                $this
                    ->productImageRepository
                    ->createImageByApi($product, $item['Images']['Primary']['Large']['URL']);

                foreach ($item['ItemInfo']['Features']['DisplayValues'] as $feature) {
                    $this
                        ->productDetailRepository
                        ->createDetailByApi($product, $feature);
                }
            }
        }
        $this->entityManager->flush();
        $output->writeln('Los datos fueron exportados con éxito.');
        return Command::SUCCESS;
    }
}
