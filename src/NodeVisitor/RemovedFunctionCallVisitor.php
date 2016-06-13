<?php

namespace Sstalle\php7cc\NodeVisitor;

use PhpParser\Node;
use Sstalle\php7cc\CompatibilityViolation\Message;
use Sstalle\php7cc\NodeAnalyzer\FunctionAnalyzer;

class RemovedFunctionCallVisitor extends AbstractVisitor
{
    const LEVEL = Message::LEVEL_ERROR;

    /**
     * @var string[]
     */
    protected $removedFunctionNames = array(
        // Removed in favor of call_user_func* functions
        'call_user_method',
        'call_user_method_array',

        // Removed in favor of the stream_set_blocking() function
        'set_socket_blocking',

        // Removed in favor of the datefmt_set_timezone()
        'datefmt_set_timezone_id',

        // Removed in favor of the mcrypt_generic_deinit() function
        'mcrypt_generic_end',

        // Replaced by mcrypt_decrypt() with a MCRYPT_MODE_* constant.
        'mcrypt_ecb',
        'mcrypt_cbc',
        'mcrypt_cfb',
        'mcrypt_ofb',

        // Magic quotes no longer available
        'set_magic_quotes_runtime',
        'magic_quotes_runtime',

        // The ereg extension was removed
        'ereg_replace',
        'ereg',
        'eregi_replace',
        'eregi',
        'split',
        'spliti',
        'sql_regcase',

        // Support for PostScript Type1 fonts has been removed from the GD extension
        'imagepsbbox',
        'imagepsencodefont',
        'imagepsextendedfont',
        'imagepsfreefont',
        'imagepsloadfont',
        'imagepsslantfont',
        'imagepstext',

        // The mysql extension removed
        'mysql_affected_rows',
        'mysql_client_encoding',
        'mysql_close',
        'mysql_connect',
        'mysql_create_db',
        'mysql_data_seek',
        'mysql_db_name',
        'mysql_db_query',
        'mysql_drop_db',
        'mysql_errno',
        'mysql_error',
        'mysql_escape_string',
        'mysql_fetch_array',
        'mysql_fetch_assoc',
        'mysql_fetch_field',
        'mysql_fetch_lengths',
        'mysql_fetch_object',
        'mysql_fetch_row',
        'mysql_field_flags',
        'mysql_field_len',
        'mysql_field_name',
        'mysql_field_seek',
        'mysql_field_table',
        'mysql_field_type',
        'mysql_free_result',
        'mysql_get_client_info',
        'mysql_get_host_info',
        'mysql_get_proto_info',
        'mysql_get_server_info',
        'mysql_info',
        'mysql_insert_id',
        'mysql_list_dbs',
        'mysql_list_fields',
        'mysql_list_processes',
        'mysql_list_tables',
        'mysql_num_fields',
        'mysql_num_rows',
        'mysql_pconnect',
        'mysql_ping',
        'mysql_query',
        'mysql_real_escape_string',
        'mysql_result',
        'mysql_select_db',
        'mysql_set_charset',
        'mysql_stat',
        'mysql_tablename',
        'mysql_thread_id',
        'mysql_unbuffered_query',

        // The mssql extension was removed
        'mssql_bind',
        'mssql_close',
        'mssql_connect',
        'mssql_data_seek',
        'mssql_execute',
        'mssql_fetch_array',
        'mssql_fetch_assoc',
        'mssql_fetch_batch',
        'mssql_fetch_field',
        'mssql_fetch_object',
        'mssql_fetch_row',
        'mssql_field_length',
        'mssql_field_name',
        'mssql_field_seek',
        'mssql_field_type',
        'mssql_free_result',
        'mssql_free_statement',
        'mssql_get_last_message',
        'mssql_guid_string',
        'mssql_init',
        'mssql_min_error_severity',
        'mssql_min_message_severity',
        'mssql_next_result',
        'mssql_num_fields',
        'mssql_num_rows',
        'mssql_pconnect',
        'mssql_query',
        'mssql_result',
        'mssql_rows_affected',
        'mssql_select_db',
    );

    /**
     * @var FunctionAnalyzer
     */
    protected $functionAnalyzer;

    /**
     * @param FunctionAnalyzer $functionAnalyzer
     */
    public function __construct(FunctionAnalyzer $functionAnalyzer)
    {
        $this->functionAnalyzer = $functionAnalyzer;
        $this->removedFunctionNames = array_flip($this->removedFunctionNames);
    }

    /**
     * {@inheritdoc}
     */
    public function enterNode(Node $node)
    {
        if (!$this->functionAnalyzer->isFunctionCallByStaticName($node, $this->removedFunctionNames)) {
            return;
        }

        /** @var Node\Expr\FuncCall $node */
        $this->addContextMessage(
            sprintf('Removed function "%s" called', $node->name->toString()),
            $node
        );
    }
}
