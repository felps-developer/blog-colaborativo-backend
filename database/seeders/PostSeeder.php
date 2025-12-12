<?php

namespace Database\Seeders;

use App\Modules\Posts\Entities\Post;
use App\Modules\Users\Entities\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class PostSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Criar ou buscar usuário
        $user = User::updateOrCreate(
            ['email' => 'teste@example.com'],
            [
                'name' => 'Usuário Teste',
                'password' => Hash::make('123456'),
            ]
        );

        // Criar 15 posts para o usuário
        $posts = [
            [
                'title' => 'Bem-vindo ao Blog Colaborativo',
                'content' => [
                    'version' => '1.0',
                    'content' => '<h1>Bem-vindo ao Blog Colaborativo</h1><p>Este é o primeiro post do nosso blog colaborativo. Aqui você pode compartilhar suas ideias, experiências e conhecimentos com a comunidade.</p><p>Esperamos que você aproveite esta plataforma!</p>'
                ]
            ],
            [
                'title' => 'Dicas para Escrever Posts Incríveis',
                'content' => [
                    'version' => '1.0',
                    'content' => '<h2>Como escrever posts que engajam</h2><p>Escrever um bom post requer algumas técnicas importantes:</p><ul><li>Use títulos chamativos</li><li>Estruture bem o conteúdo</li><li>Adicione imagens quando relevante</li><li>Seja claro e objetivo</li></ul><p>Com essas dicas, seus posts terão muito mais sucesso!</p>'
                ]
            ],
            [
                'title' => 'A Importância da Comunicação',
                'content' => [
                    'version' => '1.0',
                    'content' => '<h2>A comunicação é fundamental</h2><p>Em um mundo cada vez mais conectado, saber se comunicar bem é essencial. A comunicação eficaz pode:</p><ul><li>Melhorar relacionamentos</li><li>Aumentar a produtividade</li><li>Resolver conflitos</li><li>Fortalecer equipes</li></ul><p>Invista tempo em desenvolver suas habilidades de comunicação!</p>'
                ]
            ],
            [
                'title' => 'Tecnologia e Inovação em 2024',
                'content' => [
                    'version' => '1.0',
                    'content' => '<h2>Tendências tecnológicas</h2><p>O ano de 2024 trouxe muitas inovações interessantes:</p><ul><li>Inteligência Artificial avançada</li><li>Realidade Virtual e Aumentada</li><li>Blockchain e Web3</li><li>Sustentabilidade tecnológica</li></ul><p>Estas tecnologias estão moldando nosso futuro!</p>'
                ]
            ],
            [
                'title' => 'Dicas de Produtividade',
                'content' => [
                    'version' => '1.0',
                    'content' => '<h2>Como ser mais produtivo</h2><p>Produtividade não é sobre trabalhar mais, mas sim trabalhar melhor. Algumas estratégias:</p><ul><li>Use a técnica Pomodoro</li><li>Priorize tarefas importantes</li><li>Elimine distrações</li><li>Faça pausas regulares</li></ul><p>Com essas práticas, você verá resultados significativos!</p>'
                ]
            ],
            [
                'title' => 'Saúde Mental no Trabalho',
                'content' => [
                    'version' => '1.0',
                    'content' => '<h2>Cuidando da mente</h2><p>A saúde mental é tão importante quanto a física. No ambiente de trabalho:</p><ul><li>Estabeleça limites claros</li><li>Pratique mindfulness</li><li>Mantenha um equilíbrio vida-trabalho</li><li>Busque ajuda quando necessário</li></ul><p>Lembre-se: sua saúde vem em primeiro lugar!</p>'
                ]
            ],
            [
                'title' => 'Aprendizado Contínuo',
                'content' => [
                    'version' => '1.0',
                    'content' => '<h2>Nunca pare de aprender</h2><p>O aprendizado contínuo é essencial nos dias de hoje:</p><ul><li>Mantenha-se atualizado</li><li>Experimente novas tecnologias</li><li>Participe de comunidades</li><li>Compartilhe conhecimento</li></ul><p>O conhecimento é a melhor ferramenta que você pode ter!</p>'
                ]
            ],
            [
                'title' => 'Trabalho em Equipe Eficaz',
                'content' => [
                    'version' => '1.0',
                    'content' => '<h2>Colaboração que funciona</h2><p>Um bom trabalho em equipe requer:</p><ul><li>Comunicação clara</li><li>Respeito mútuo</li><li>Definição de papéis</li><li>Objetivos compartilhados</li></ul><p>Juntos somos mais fortes!</p>'
                ]
            ],
            [
                'title' => 'Gestão de Tempo',
                'content' => [
                    'version' => '1.0',
                    'content' => '<h2>Administre seu tempo com sabedoria</h2><p>O tempo é nosso recurso mais valioso. Para gerenciá-lo bem:</p><ul><li>Planeje seu dia</li><li>Use ferramentas de organização</li><li>Evite a procrastinação</li><li>Reavalie regularmente</li></ul><p>O tempo bem gerenciado é tempo bem investido!</p>'
                ]
            ],
            [
                'title' => 'Criatividade e Inovação',
                'content' => [
                    'version' => '1.0',
                    'content' => '<h2>Desperte sua criatividade</h2><p>A criatividade pode ser desenvolvida:</p><ul><li>Pratique brainstorming</li><li>Explore novas perspectivas</li><li>Permita-se errar</li><li>Inspire-se em diferentes áreas</li></ul><p>A inovação nasce da criatividade!</p>'
                ]
            ],
            [
                'title' => 'Networking Profissional',
                'content' => [
                    'version' => '1.0',
                    'content' => '<h2>Construa sua rede</h2><p>Networking é fundamental para o crescimento profissional:</p><ul><li>Participe de eventos</li><li>Use redes sociais profissionais</li><li>Mantenha contatos</li><li>Ofereça valor primeiro</li></ul><p>Sua rede é seu patrimônio!</p>'
                ]
            ],
            [
                'title' => 'Liderança Inspiradora',
                'content' => [
                    'version' => '1.0',
                    'content' => '<h2>Seja um líder melhor</h2><p>Uma boa liderança envolve:</p><ul><li>Escutar ativamente</li><li>Dar feedback construtivo</li><li>Reconhecer conquistas</li><li>Liderar pelo exemplo</li></ul><p>Líderes inspiram, não apenas comandam!</p>'
                ]
            ],
            [
                'title' => 'Sustentabilidade no Dia a Dia',
                'content' => [
                    'version' => '1.0',
                    'content' => '<h2>Pequenas ações, grandes impactos</h2><p>Podemos fazer a diferença:</p><ul><li>Reduza o consumo</li><li>Recicle adequadamente</li><li>Use transporte sustentável</li><li>Consuma conscientemente</li></ul><p>Cada ação conta para um futuro melhor!</p>'
                ]
            ],
            [
                'title' => 'Desenvolvimento Pessoal',
                'content' => [
                    'version' => '1.0',
                    'content' => '<h2>Invista em você mesmo</h2><p>O desenvolvimento pessoal é uma jornada contínua:</p><ul><li>Leia regularmente</li><li>Pratique autoconhecimento</li><li>Estabeleça metas</li><li>Celebre pequenas vitórias</li></ul><p>Você é seu maior investimento!</p>'
                ]
            ],
            [
                'title' => 'Futuro do Trabalho',
                'content' => [
                    'version' => '1.0',
                    'content' => '<h2>O que esperar</h2><p>O mundo do trabalho está evoluindo:</p><ul><li>Automação inteligente</li><li>Trabalho remoto híbrido</li><li>Habilidades adaptáveis</li><li>Foco em propósito</li></ul><p>Adapte-se e prospere no novo cenário!</p>'
                ]
            ],
        ];

        foreach ($posts as $postData) {
            Post::updateOrCreate(
                [
                    'title' => $postData['title'],
                    'author_id' => $user->id,
                ],
                [
                    'content' => $postData['content'],
                ]
            );
        }

        $this->command->info('15 posts criados com sucesso para o usuário teste@example.com!');
    }
}

