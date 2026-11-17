<?php

namespace Database\Seeders;

use App\Enums\EventType;
use App\Models\CommunityEvent;
use Illuminate\Database\Seeder;

class CommunityEventSeeder extends Seeder
{
    /**
     * Placeholder events around today: three still to come and three already held. Replace through the admin panel.
     */
    public function run(): void
    {
        $images = [...SeedImages::in('interior'), ...SeedImages::in('architecture'), ...SeedImages::in('design')];

        $events = [
            [
                'slug' => 'material-matters',
                'title' => ['en' => 'Material Matters', 'id' => 'Soal Material'],
                'type' => EventType::Talk,
                'days' => 12,
                'hours' => [19, 21],
                'venue' => 'Studio Kolektif',
                'city' => 'Jakarta',
                'summary' => [
                    'en' => 'Three members on choosing local materials that age well.',
                    'id' => 'Tiga anggota berbagi cara memilih material lokal yang menua dengan baik.',
                ],
                'description' => [
                    'en' => "An evening talk on teak, terrazzo, rattan and lime plaster: where to source them, how to detail them and how they change over the years.\n\nThe talk is followed by a short Q&A and an open table with material samples.",
                    'id' => "Bincang malam tentang jati, teraso, rotan, dan plester kapur: dari mana mendapatkannya, bagaimana mendetailnya, dan bagaimana material itu berubah seiring waktu.\n\nAcara dilanjutkan dengan tanya jawab singkat dan meja terbuka berisi sampel material.",
                ],
            ],
            [
                'slug' => 'joinery-workshop',
                'title' => ['en' => 'Joinery Workshop', 'id' => 'Lokakarya Sambungan Kayu'],
                'type' => EventType::Workshop,
                'days' => 26,
                'hours' => [9, 16],
                'venue' => 'Bengkel Kayu Jepara',
                'city' => 'Jepara',
                'summary' => [
                    'en' => 'A hands-on day with Jepara carpenters on traditional wood joints.',
                    'id' => 'Sehari praktik langsung bersama tukang kayu Jepara tentang sambungan kayu tradisional.',
                ],
                'description' => [
                    'en' => "Learn three classic joints and build a small stool to take home. All tools and timber are provided.\n\nPlaces are limited to twelve participants.",
                    'id' => "Pelajari tiga sambungan klasik dan buat bangku kecil untuk dibawa pulang. Semua alat dan kayu disediakan.\n\nTempat terbatas untuk dua belas peserta.",
                ],
            ],
            [
                'slug' => 'the-latest-works-2026',
                'title' => ['en' => 'The Latest Works 2026', 'id' => 'The Latest Works 2026'],
                'type' => EventType::Exhibition,
                'days' => 41,
                'hours' => [10, 20],
                'end_days' => 55,
                'venue' => 'Galeri Ruang Tengah',
                'city' => 'Bandung',
                'summary' => [
                    'en' => 'Our yearly exhibition of models, drawings and prototypes by the members.',
                    'id' => 'Pameran tahunan berisi maket, gambar, dan prototipe karya para anggota.',
                ],
                'description' => [
                    'en' => "Two weeks of architecture models, interior studies and product prototypes from the collective's members.\n\nGuided tours run every Saturday at 16.00.",
                    'id' => "Dua minggu pameran maket arsitektur, studi interior, dan prototipe produk dari para anggota kolektif.\n\nTur berpemandu diadakan setiap Sabtu pukul 16.00.",
                ],
            ],
            [
                'slug' => 'open-studio-night',
                'title' => ['en' => 'Open Studio Night', 'id' => 'Malam Studio Terbuka'],
                'type' => EventType::Meetup,
                'days' => -18,
                'hours' => [18, 22],
                'venue' => 'Studio Kolektif',
                'city' => 'Jakarta',
                'summary' => [
                    'en' => 'Members opened their desks and works in progress to the public.',
                    'id' => 'Para anggota membuka meja kerja dan karya yang sedang berjalan untuk publik.',
                ],
                'description' => [
                    'en' => 'An informal evening of drawings, models and conversations with the designers behind them.',
                    'id' => 'Malam santai berisi gambar, maket, dan obrolan bersama para desainer di baliknya.',
                ],
            ],
            [
                'slug' => 'designing-with-light',
                'title' => ['en' => 'Designing with Light', 'id' => 'Merancang dengan Cahaya'],
                'type' => EventType::Talk,
                'days' => -47,
                'hours' => [19, 21],
                'venue' => 'Perpustakaan Kota',
                'city' => 'Surabaya',
                'summary' => [
                    'en' => 'Daylight, shade and lamps in tropical houses.',
                    'id' => 'Cahaya alami, bayangan, dan lampu di rumah tropis.',
                ],
                'description' => [
                    'en' => 'A talk on how openings, overhangs and lamps shape the mood of a room through the day.',
                    'id' => 'Bincang tentang bagaimana bukaan, teritisan, dan lampu membentuk suasana ruang sepanjang hari.',
                ],
            ],
            [
                'slug' => 'sketch-walk-kota-tua',
                'title' => ['en' => 'Sketch Walk: Kota Tua', 'id' => 'Jalan Sketsa: Kota Tua'],
                'type' => EventType::Meetup,
                'days' => -90,
                'hours' => [7, 11],
                'venue' => 'Taman Fatahillah',
                'city' => 'Jakarta',
                'summary' => [
                    'en' => 'A morning of drawing the old town together.',
                    'id' => 'Pagi bersama menggambar kawasan kota lama.',
                ],
                'description' => [
                    'en' => 'Members and friends sketched facades, arcades and street corners, then shared their drawings over coffee.',
                    'id' => 'Para anggota dan teman menggambar fasad, selasar, dan sudut jalan, lalu berbagi hasil sketsa sambil minum kopi.',
                ],
            ],
        ];

        foreach ($events as $index => $event) {
            $startsAt = now()->addDays($event['days'])->setTime($event['hours'][0], 0);
            $endsAt = isset($event['end_days'])
                ? now()->addDays($event['end_days'])->setTime($event['hours'][1], 0)
                : $startsAt->copy()->setTime($event['hours'][1], 0);

            $communityEvent = CommunityEvent::updateOrCreate(
                ['slug' => $event['slug']],
                [
                    'title' => $event['title'],
                    'type' => $event['type'],
                    'summary' => $event['summary'],
                    'description' => $event['description'],
                    'starts_at' => $startsAt,
                    'ends_at' => $endsAt,
                    'venue' => $event['venue'],
                    'city' => $event['city'],
                    'registration_url' => $event['days'] > 0 ? 'https://example.com/events/'.$event['slug'] : null,
                    'is_published' => true,
                ],
            );

            $image = $images[($index * 7 + 2) % max(count($images), 1)] ?? null;

            if ($image !== null && ! $communityEvent->hasMedia('cover')) {
                $communityEvent->addMedia($image)->preservingOriginal()->toMediaCollection('cover');
            }
        }
    }
}
