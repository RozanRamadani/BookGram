<?php

namespace App\Http\Controllers;

use Barryvdh\DomPDF\Facade\Pdf;

class PDFController extends Controller
{
    /**
     * Generate Certificate PDF (Landscape A4)
     */
    public function generateCertificate()
    {
        $data = [
            'title' => 'SERTIFIKAT',
            'number' => '3353/B/WPK/2026',
            'recipient_name' => 'Ilham Joseph',
            'event_name' => 'Membalik Tren Global : Menjawab Epidemi Penyakit Tidak Menular Melalui Revolusi Gaya Hidup dan Kekuatan Kesehatan Masyarakat',
            'role' => 'WAKIL KETUA PELAKSANA',
            'event_detail' => 'Seminar Nasional dengan tema',
            'organizer' => 'Program Studi Kesehatan Masyarakat FIKIA Universitas Airlangga',
            'date' => 'Sabtu, 18 Oktober 2025',
            'issued_date' => date('d F Y'),
        ];

        // Memuat view PDF dengan data dan mengatur ukuran kertas serta orientasi
        $pdf = Pdf::loadView('pdf.certificate', $data)
            ->setPaper('a4', 'landscape');

        // Menampilkan PDF di browser
        return $pdf->stream('Sertifikat_' . str_replace(' ', '_', $data['recipient_name']) . '.pdf');
    }

    /**
     * Generate Invitation/Announcement PDF (Portrait A4 with Header)
     */
    public function generateInvitation()
    {
        $data = [
            'title' => 'UNIVERSITAS AIRLANGGA',
            'subtitle' => 'FAKULTAS VOKASI',
            'address' => 'Kampus B Jl. Dharmawangsa Dalam Surabaya 60286 Telp. (031) 5033869 Fax (031) 5033156',
            'website' => 'https://vokasi.unair.ac.id',
            'email' => 'info@vokasi.unair.ac.id',
            'number' => '556/B/DST/UN3.FV/TU.01.00/2026',
            'attachment' => 'Satu Lembar',
            'subject' => 'Undangan',
            'date' => '13 Januari 2026',
            'recipients' => [
                'Para Wakil Dekan',
                'Para Ketua Departemen',
                'Para Sekretaris Departemen',
                'Para Koordinator Program Studi',
                'Kepala Bagian Tata Usaha',
                'Para Kepala Subbagian',
                'Seluruh Dosen',
                'Seluruh Tenaga Kependidikan',
                'Fakultas Vokasi Universitas Airlangga'
            ],
            'content' => 'Dalam rangka mempererat tali silaturahmi serta mengawali kegiatan tahun 2026, Fakultas Vokasi Universitas Airlangga akan menyelenggarakan Silaturahmi Awal Tahun Keluarga Besar Fakultas Vokasi. Sehubungan dengan hal tersebut, kami mengundang Bapak/Ibu untuk hadir pada kegiatan yang akan dilaksanakan pada:',
            'event_day' => 'Selasa, 20 Januari 2026',
            'event_time' => '10.00 – 13.00 WIB',
            'event_location' => 'Aula Gedung A Lt.3 Fakultas Vokasi Universitas Airlangga',
            'event_agenda' => 'Silaturahmi Awal Tahun Keluarga Besar Fakultas Vokasi',
            'closing' => 'Demikian undangan ini kami sampaikan. Atas perhatian dan kehadiran Bapak/Ibu, kami ucapkan terima kasih.',
        ];

        // Memuat view PDF dengan data dan mengatur ukuran kertas serta orientasi
        $pdf = Pdf::loadView('pdf.invitation', $data)
            ->setPaper('a4', 'portrait');

        // Menampilkan PDF di browser
        return $pdf->stream('Undangan_Fakultas_Vokasi.pdf');
    }
}
