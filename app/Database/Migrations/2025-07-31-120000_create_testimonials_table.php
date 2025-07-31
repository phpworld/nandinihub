<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTestimonialsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'name' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => false,
            ],
            'email' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'position' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'company' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'testimonial' => [
                'type' => 'TEXT',
                'null' => false,
            ],
            'rating' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 5,
                'comment'    => 'Rating from 1 to 5 stars',
            ],
            'image' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'comment'    => 'Profile image filename',
            ],
            'location' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'is_featured' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
                'comment'    => '1 for featured testimonials',
            ],
            'is_active' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 1,
                'comment'    => '1 for active, 0 for inactive',
            ],
            'sort_order' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 0,
                'comment'    => 'Display order',
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('is_featured');
        $this->forge->addKey('is_active');
        $this->forge->addKey('sort_order');
        $this->forge->createTable('testimonials');

        // Insert sample testimonials
        $data = [
            [
                'name' => 'Sarah Johnson',
                'position' => 'Wellness Coach',
                'company' => 'Mindful Living Center',
                'testimonial' => 'The microdose mushrooms from this store have been a game-changer for my mental clarity and creativity. The quality is exceptional and the effects are exactly what I was looking for.',
                'rating' => 5,
                'location' => 'California, USA',
                'is_featured' => 1,
                'is_active' => 1,
                'sort_order' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'Michael Chen',
                'position' => 'Artist',
                'company' => 'Creative Studios',
                'testimonial' => 'As an artist, I was looking for something to enhance my creative process. These magic mushrooms have opened up new dimensions in my work. Highly recommended!',
                'rating' => 5,
                'location' => 'New York, USA',
                'is_featured' => 1,
                'is_active' => 1,
                'sort_order' => 2,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'Emma Rodriguez',
                'position' => 'Therapist',
                'company' => 'Healing Minds Clinic',
                'testimonial' => 'I have been recommending these products to my clients for therapeutic purposes. The consistent quality and positive results speak for themselves.',
                'rating' => 5,
                'location' => 'Colorado, USA',
                'is_featured' => 1,
                'is_active' => 1,
                'sort_order' => 3,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
        ];

        $this->db->table('testimonials')->insertBatch($data);
    }

    public function down()
    {
        $this->forge->dropTable('testimonials');
    }
}
