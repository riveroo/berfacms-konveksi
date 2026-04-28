<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LandingReviewSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $reviews = [
            [
                'review_text' => 'Kualitas produk sangat memuaskan, bordir rapi dan kain terasa premium. Sangat merekomendasikan untuk pembuatan seragam skala besar.',
                'reviewer_name' => 'Budi Santoso',
                'client_name' => 'PT Makmur Jaya',
                'sort_order' => 1,
            ],
            [
                'review_text' => 'Pengerjaan tepat waktu sesuai deadline. Komunikasi dengan tim sangat lancar. Pasti akan order lagi untuk kebutuhan perusahaan kami.',
                'reviewer_name' => 'Siti Aminah',
                'client_name' => 'CV Sukses Selalu',
                'sort_order' => 2,
            ],
            [
                'review_text' => 'Desain sesuai dengan yang kami harapkan. Warna sablon tajam dan tidak mudah luntur meski sudah dicuci berkali-kali.',
                'reviewer_name' => 'Andi Wijaya',
                'client_name' => 'Tech Nusantara',
                'sort_order' => 3,
            ],
            [
                'review_text' => 'Harga kompetitif namun kualitas tetap terjaga. Pelayanan ramah dan responsif terhadap perubahan desain menit terakhir.',
                'reviewer_name' => 'Rini Lestari',
                'client_name' => 'Koperasi Sejahtera',
                'sort_order' => 4,
            ],
            [
                'review_text' => 'Bahan kaos yang digunakan sangat nyaman dan adem. Tim kami sangat suka dengan seragam barunya.',
                'reviewer_name' => 'Deni Pratama',
                'client_name' => 'Event Organizer JKT',
                'sort_order' => 5,
            ],
            [
                'review_text' => 'Proses pemesanan mudah dan transparan. Laporan progres produksi diberikan secara berkala.',
                'reviewer_name' => 'Maya Sari',
                'client_name' => 'Yayasan Pendidikan',
                'sort_order' => 6,
            ],
        ];

        foreach ($reviews as $review) {
            \App\Models\LandingReview::create($review);
        }
    }
}
