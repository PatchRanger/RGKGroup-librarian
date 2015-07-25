<?php

use yii\db\Schema;
use yii\db\Migration;

class m150725_053916_init extends Migration
{
    public function up()
    {
        $this->createTable('user', [
			'id'=>'pk',
			'status'=>'tinyint(1)',
			'role'=>'tinyint(1)',
			'email'=>'string',
			'username'=>'string',
			'password'=>'string',
			'create_time'=>'integer',
			'update_time'=>'integer',
		//], 'ENGINE=InnoDB CHARACTER SET=utf8 COLLATE=utf8_general_ci');
		]);

        $this->createTable('author', [
			'id'=>'pk',
			'firstname'=>'string',
			'lastname'=>'string',
		//], 'ENGINE=InnoDB CHARACTER SET=utf8 COLLATE=utf8_general_ci');
		]);

        $this->createTable('book', [
			'id'=>'pk',
			'name'=>'string',
			'preview'=>'string',
			'author_id'=>'integer',
			'date'=>'integer',
			'date_create'=>'integer',
			'date_update'=>'integer',
		//], 'ENGINE=InnoDB CHARACTER SET=utf8 COLLATE=utf8_general_ci');
		]);
    }

    public function down()
    {
        $this->dropTable('user');
        $this->dropTable('author');
        $this->dropTable('book');
    }
}
