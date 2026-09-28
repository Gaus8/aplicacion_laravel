<?php

namespace Database\Seeders;

use App\Models\AboutPage;
use App\Models\Banner;
use App\Models\Category;
use App\Models\Post;
use App\Models\Service;
use App\Models\SocialLink;
use App\Models\TeamMember;
use App\Models\Testimonial;
use App\Models\Video;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class CompanyContentSeeder extends Seeder
{
    public function run(): void
    {
        $this->installSampleImages();

        Banner::query()->updateOrCreate(
            ['title' => 'Tecnología que impulsa lo que sigue'],
            [
                'subtitle' => 'Diseñamos, construimos y protegemos soluciones digitales para empresas con grandes planes.',
                'image_path' => 'seed/usf-tech-hero.svg',
                'image_alt' => 'Ilustración abstracta de tecnología, datos y conexiones digitales en tonos azul y violeta',
                'cta_label' => 'Hablemos de tu proyecto',
                'cta_url' => '/contacto',
                'position' => 0,
                'active' => true,
                'starts_at' => null,
                'ends_at' => null,
            ],
        );

        $services = [
            ['title' => 'Desarrollo a medida', 'slug' => 'desarrollo-a-medida', 'summary' => 'Software alineado con la forma en que trabaja tu empresa.', 'description' => 'Creamos aplicaciones web, plataformas internas e integraciones que resuelven necesidades reales. Acompañamos cada etapa, desde el descubrimiento hasta la evolución del producto.', 'position' => 0],
            ['title' => 'Consultoría cloud', 'slug' => 'consultoria-cloud', 'summary' => 'Infraestructura escalable, eficiente y preparada para crecer.', 'description' => 'Diseñamos estrategias de nube, modernizamos cargas de trabajo y automatizamos operaciones para mejorar la disponibilidad y controlar los costos.', 'position' => 1],
            ['title' => 'Ciberseguridad', 'slug' => 'ciberseguridad', 'summary' => 'Protección práctica para tus sistemas, datos y equipos.', 'description' => 'Identificamos riesgos, fortalecemos aplicaciones y establecemos controles que ayudan a prevenir incidentes y responder con confianza.', 'position' => 2],
            ['title' => 'Datos e inteligencia artificial', 'slug' => 'datos-inteligencia-artificial', 'summary' => 'Convierte la información de tu negocio en mejores decisiones.', 'description' => 'Implementamos analítica, automatización e inteligencia artificial responsable para encontrar oportunidades y reducir tareas repetitivas.', 'position' => 3],
        ];

        foreach ($services as $service) {
            Service::query()->updateOrCreate(
                ['slug' => $service['slug']],
                $service + ['cta_label' => 'Conversemos', 'cta_url' => '/contacto', 'active' => true],
            );
        }

        AboutPage::query()->updateOrCreate(
            ['title' => 'Tecnología con propósito, equipo y visión'],
            [
                'subtitle' => 'Somos USF Tech Solutions, un equipo que convierte retos de negocio en productos digitales confiables.',
                'body' => 'Nacimos con una idea sencilla: la tecnología funciona mejor cuando parte de las personas y los objetivos que debe ayudar. Desde entonces, acompañamos a organizaciones en sus procesos de transformación, combinando estrategia, ingeniería y colaboración cercana.\n\nTrabajamos con equipos multidisciplinarios y prácticas transparentes para entregar soluciones útiles, seguras y sostenibles en el tiempo.',
                'mission' => 'Ayudar a las organizaciones a avanzar con soluciones digitales seguras, simples de usar y hechas para generar valor duradero.',
                'vision' => 'Ser el socio tecnológico de confianza para empresas que construyen un futuro más conectado, eficiente y humano.',
                'image_path' => 'seed/usf-tech-hero.svg',
                'image_alt' => 'Ilustración panorámica de las conexiones y plataformas digitales que diseñamos',
                'active' => true,
            ],
        );

        $categories = [
            ['name' => 'Ingeniería de software', 'slug' => 'ingenieria-de-software', 'description' => 'Arquitectura, desarrollo y calidad de productos digitales.'],
            ['name' => 'Nube y plataformas', 'slug' => 'nube-y-plataformas', 'description' => 'Infraestructura, operaciones y servicios cloud.'],
            ['name' => 'Seguridad digital', 'slug' => 'seguridad-digital', 'description' => 'Buenas prácticas para proteger productos y datos.'],
        ];

        foreach ($categories as $category) {
            Category::query()->updateOrCreate(['slug' => $category['slug']], $category + ['active' => true]);
        }

        $posts = [
            [
                'title' => 'De la idea al producto: cómo construir software que sí resuelve',
                'slug' => 'de-la-idea-al-producto-software-que-resuelve',
                'category' => 'ingenieria-de-software',
                'excerpt' => 'Un buen producto digital empieza por entender el problema. Te contamos cómo conectar estrategia, diseño e ingeniería desde el primer día.',
                'body' => "Los proyectos de software más exitosos no comienzan con una lista de funcionalidades, sino con una conversación sobre las personas y los procesos que necesitan mejorar.\n\nEn USF Tech Solutions trabajamos con ciclos cortos de descubrimiento y entrega. Validamos supuestos temprano, construimos una base técnica clara y medimos el valor que cada incremento aporta al negocio.\n\nEl resultado es un producto que puede evolucionar con confianza: útil para quienes lo usan, mantenible para quienes lo construyen y alineado con los objetivos de la organización.",
                'cover_path' => 'seed/article-software.svg',
                'cover_alt' => 'Ilustración de módulos de software conectados sobre un fondo azul',
                'days_ago' => 8,
            ],
            [
                'title' => 'Migrar a la nube con una estrategia clara',
                'slug' => 'migrar-a-la-nube-con-estrategia',
                'category' => 'nube-y-plataformas',
                'excerpt' => 'La nube aporta agilidad cuando la migración responde a necesidades concretas y cuenta con una hoja de ruta medible.',
                'body' => "Una migración cloud bien planeada combina objetivos de negocio, preparación de aplicaciones y gobierno de costos. Antes de mover cargas, conviene identificar dependencias, requisitos de disponibilidad y controles de seguridad.\n\nUna estrategia por etapas permite aprender con servicios acotados, automatizar tareas repetibles y reducir riesgos operativos. La observabilidad y la optimización continua ayudan a mantener el valor después del lanzamiento.\n\nCada organización tiene un punto de partida distinto. Por eso, el mejor plan es el que prioriza resultados verificables y deja espacio para adaptarse.",
                'cover_path' => 'seed/article-cloud.svg',
                'cover_alt' => 'Ilustración de una nube conectada a servicios digitales',
                'days_ago' => 15,
            ],
            [
                'title' => 'Seguridad desde el diseño: hábitos que fortalecen cada entrega',
                'slug' => 'seguridad-desde-el-diseno-habitos',
                'category' => 'seguridad-digital',
                'excerpt' => 'Integrar seguridad al trabajo diario ayuda a detectar riesgos antes y a crear experiencias digitales más confiables.',
                'body' => "La seguridad efectiva no es una revisión aislada al final del proyecto. Se construye con decisiones cotidianas: limitar accesos, validar entradas, proteger secretos y mantener dependencias actualizadas.\n\nLos equipos pueden incorporar análisis automatizados, revisión de cambios y modelos de amenazas ligeros en su flujo habitual. Estas prácticas hacen visibles los riesgos sin frenar la entrega.\n\nCon prioridades claras y aprendizaje continuo, cada lanzamiento fortalece la confianza de clientes, colaboradores y socios.",
                'cover_path' => 'seed/article-security.svg',
                'cover_alt' => 'Ilustración de un escudo que protege una red de servicios',
                'days_ago' => 23,
            ],
        ];

        foreach ($posts as $postData) {
            $category = Category::query()->where('slug', $postData['category'])->firstOrFail();
            $publishedAt = now()->subDays($postData['days_ago'])->startOfDay();
            unset($postData['category'], $postData['days_ago']);

            Post::query()->updateOrCreate(
                ['slug' => $postData['slug']],
                $postData + [
                    'category_id' => $category->id,
                    'author_id' => null,
                    'status' => 'published',
                    'published_at' => $publishedAt,
                    'cover_path' => null,
                    'cover_alt' => null,
                ],
            );
        }

        $videos = [
            ['external_id' => 'M7lc1UVf-VE', 'title' => 'Tecnología que transforma negocios', 'description' => 'Una introducción a la innovación digital y a las oportunidades de construir mejores experiencias.', 'position' => 0],
            ['external_id' => 'aqz-KE-bpKQ', 'title' => 'Historias de innovación y colaboración', 'description' => 'Una muestra audiovisual para descubrir nuevas ideas y formas de trabajar en equipo.', 'position' => 1],
        ];

        foreach ($videos as $video) {
            Video::query()->updateOrCreate(
                ['provider' => 'youtube', 'external_id' => $video['external_id']],
                $video + ['video_url' => 'https://www.youtube.com/watch?v='.$video['external_id'], 'active' => true],
            );
        }

        $members = [
            ['name' => 'Valentina Ríos', 'role' => 'Directora de Tecnología', 'bio' => 'Lidera la estrategia tecnológica y acompaña a los equipos para convertir retos complejos en soluciones simples y escalables.', 'email' => 'valentina@usftechsolutions.com', 'image' => 'team-valentina.svg'],
            ['name' => 'Andrés Mejía', 'role' => 'Líder de Ingeniería Cloud', 'bio' => 'Diseña plataformas resilientes y ayuda a las organizaciones a aprovechar la nube con seguridad, eficiencia y foco en el negocio.', 'email' => 'andres@usftechsolutions.com', 'image' => 'team-andres.svg'],
            ['name' => 'Camila Torres', 'role' => 'Directora de Ciberseguridad', 'bio' => 'Integra la seguridad al ciclo de vida del software y promueve una cultura de prevención y aprendizaje continuo.', 'email' => 'camila@usftechsolutions.com', 'image' => 'team-camila.svg'],
        ];

        foreach ($members as $position => $member) {
            $image = $member['image'];
            unset($member['image']);

            TeamMember::query()->updateOrCreate(
                ['email' => $member['email']],
                $member + [
                    'profile_url' => null,
                    'image_path' => 'seed/'.$image,
                    'image_alt' => 'Retrato ilustrado de '.$member['name'],
                    'position' => $position,
                    'active' => true,
                ],
            );
        }

        $testimonials = [
            ['person_name' => 'Mariana López', 'role' => 'Gerente de Operaciones', 'organization' => 'Nexa Logística', 'quote' => 'USF entendió nuestros procesos antes de proponer tecnología. La nueva plataforma redujo tareas manuales y nos dio una operación mucho más clara.'],
            ['person_name' => 'Julián Herrera', 'role' => 'Director de Producto', 'organization' => 'Financia Nova', 'quote' => 'El equipo combinó criterio técnico y comunicación cercana. Pudimos lanzar con confianza y hoy tenemos una solución preparada para crecer.'],
            ['person_name' => 'Paola Cárdenas', 'role' => 'Líder de Transformación Digital', 'organization' => 'Grupo Horizonte', 'quote' => 'Su acompañamiento en nube y seguridad nos ayudó a modernizar servicios críticos sin perder de vista a las personas usuarias.'],
        ];

        foreach ($testimonials as $position => $testimonial) {
            Testimonial::query()->updateOrCreate(
                ['person_name' => $testimonial['person_name'], 'organization' => $testimonial['organization']],
                $testimonial + ['position' => $position, 'active' => true],
            );
        }

        $socialLinks = [
            ['platform' => 'x', 'label' => 'X', 'url' => 'https://x.com/usftechsolutions'],
            ['platform' => 'linkedin', 'label' => 'LinkedIn', 'url' => 'https://www.linkedin.com/company/usf-tech-solutions/'],
            ['platform' => 'github', 'label' => 'GitHub', 'url' => 'https://github.com/usf-tech-solutions'],
        ];

        foreach ($socialLinks as $position => $socialLink) {
            SocialLink::query()->updateOrCreate(
                ['platform' => $socialLink['platform']],
                $socialLink + ['position' => $position, 'active' => true],
            );
        }
    }

    private function installSampleImages(): void
    {
        foreach (['usf-tech-hero.svg', 'team-valentina.svg', 'team-andres.svg', 'team-camila.svg', 'article-software.svg', 'article-cloud.svg', 'article-security.svg'] as $image) {
            $source = __DIR__.'/assets/'.$image;
            $contents = is_file($source) ? file_get_contents($source) : false;

            if ($contents === false || ! Storage::disk('public')->put('seed/'.$image, $contents)) {
                throw new \RuntimeException("No fue posible instalar la imagen de ejemplo {$image}.");
            }
        }
    }
}
