<?php

return [

    'install' => function ($app) {

		$util = $app['db']->getUtility();


		if ($util->tableExists('@emailsender_emailtext') === false) {
			$util->createTable('@emailsender_emailtext', function ($table) {
				$table->addColumn('id', 'integer', ['unsigned' => true, 'length' => 10, 'autoincrement' => true]);
				$table->addColumn('type', 'string', ['length' => 64]);
				$table->addColumn('description', 'string', ['length' => 255, 'notnull' => false]);
				$table->addColumn('subject', 'string', ['length' => 255, 'notnull' => false]);
				$table->addColumn('content', 'text', ['notnull' => false]);
				$table->addColumn('roles', 'simple_array', ['notnull' => false]);
				$table->addColumn('data', 'json_array', ['notnull' => false]);
				$table->setPrimaryKey(['id']);
			});
		}

		if ($util->tableExists('@emailsender_emaillog') === false) {
			$util->createTable('@emailsender_emaillog', function ($table) {
				$table->addColumn('id', 'integer', ['unsigned' => true, 'length' => 10, 'autoincrement' => true]);
				$table->addColumn('sent', 'datetime');
				$table->addColumn('ext_key', 'string', ['notnull' => false]);
				$table->addColumn('from_name', 'string', ['notnull' => false]);
				$table->addColumn('from_email', 'string');
				$table->addColumn('recipients', 'simple_array');
				$table->addColumn('cc', 'simple_array', ['notnull' => false]);
				$table->addColumn('bcc', 'simple_array', ['notnull' => false]);
				$table->addColumn('type', 'string', ['length' => 64]);
				$table->addColumn('subject', 'string', ['length' => 255, 'notnull' => false]);
				$table->addColumn('content', 'text', ['notnull' => false]);
				$table->addColumn('data', 'json_array', ['notnull' => false]);
				$table->setPrimaryKey(['id']);
				$table->addIndex(['ext_key'], 'EMAILSENDER_EMAILLOG_EXT_KEY');
			});
		}

    },

	'uninstall' => function ($app) {

        $util = $app['db']->getUtility();

        if ($util->tableExists('@emailsender_emailtext')) {
            $util->dropTable('@emailsender_emailtext');
        }
        if ($util->tableExists('@emailsender_emaillog')) {
            $util->dropTable('@emailsender_emaillog');
        }

		// remove the config
		$app['config']->remove('bixie/emailsender');

	},

	'updates' => [


	]

];