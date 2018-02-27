<?php
class GlosowaniaSzczegoly extends WP_List_Table {
	static $cache;
	public $user_id;

    function __construct(){
        global $status, $page;
                
        //Set parent defaults
        parent::__construct( array(
            'singular'  => 'glosowanie',     //singular name of the listed records
            'plural'    => 'glosowania',    //plural name of the listed records
            'ajax'      => false        //does this table support ajax?
        ) );
		
		$this->user_id = (int) @ $_GET['user'];
        
    }

	function cache( $type, $key, $val = null ){
		$cache = & self::$cache[ $type ][ $key ];
		if( ! isset( $cache ) && $val !== null )
			$cache = $val;
		return $cache;
	}

	function column_default( $log, $col ){
		global $wpdb;

		// вывод
		if(0){}
		elseif( $col == 'qid2' ){
			return esc_html($log->qid2);
		}
		elseif( $col == 'ip_info' ){
			// обновим данные IP если их нет и прошло больше суток с последней попытки
			if( $log->ip ){
				if( ! $log->ip_info || ( is_numeric($log->ip_info) && (time()-DAY_IN_SECONDS) > $log->ip_info ) ){
					$log->ip_info = Democracy_Poll::ip_info_format( $log->ip );

					$wpdb->update( $wpdb->democracy_log, array('ip_info'=>$log->ip_info), array('logid'=>$log->logid) );
				}

				if( $log->ip_info && ! is_numeric($log->ip_info) ){
					list( $country_name, $county_code, $city ) = explode(',', $log->ip_info);

					// css background position
					if( ! $flagcss = $this->cache('flagcss', 'flagcss' ) )
						$flagcss = $this->cache('flagcss', 'flagcss', file_get_contents(DEMOC_PATH .'admin/country_flags/flags.css') );
					preg_match("~flag-". strtolower($county_code) ." \{([^}]+)\}~", $flagcss, $mm );
					$bg_pos = @ $mm[1] ?: '';

					$country_img = $bg_pos ? '<span title="'. $country_name . ($city?", $city":'') .'" style="cursor:help; display:inline-block; width:16px; height:11px; background:url('. DEMOC_URL .'admin/country_flags/flags.png) no-repeat; '. $bg_pos .'"></span> ' : '';
				}
			}

			return @ $country_img ? $country_img .' <span style="opacity:0.7">'. $country_name . ($city?", $city":'') .'</span>' : '';
		}
		elseif( $col == 'qid' ){
			if( ! $poll = $this->cache('polls', $log->qid ) )
				$poll = $this->cache('polls', $log->qid, DemPoll::get_poll( $log->qid ) );
			$actions = '';
			if( $has_logs = democr()->opt('keep_logs') && $wpdb->get_var("SELECT qid FROM $wpdb->democracy_log WHERE qid = ". intval($log->qid) ." LIMIT 1") ) {
				$actions = '<div class="row-actions">';
				$actions .= '<span class="edit">'.sprintf('<a href="?page=%s&action=%s&poll=%s">Szczegóły głosowania</a>',$_REQUEST['page'],'details',$log->qid).'</span>';
				$actions .= '</div>';
			}

		return democr()->kses_html( $poll->question ) . $actions;
		}
		elseif( $col == 'userid' ){
			if( ! $user = $this->cache('users', $log->userid ) )
				$user = $this->cache('users', $log->userid, $wpdb->get_row("SELECT * FROM $wpdb->users WHERE ID = ". (int) $log->userid ) );

			return esc_html( @ $user->user_nicename );
		}
		elseif( $col == 'expire' ){
			return date('Y-m-d H:i:s', $log->expire + (get_option('gmt_offset') * HOUR_IN_SECONDS) );
		}
		elseif( $col == 'aids' ){
			$out = array();
			foreach( explode(',', $log->aids ) as $aid ){
				if( ! $answ = $this->cache('answs', $aid ) )
					$answ = $this->cache('answs', $aid, $wpdb->get_row("SELECT * FROM $wpdb->democracy_a WHERE aid = ". (int) $aid ) );

				$new = democr()->is_new_answer($answ) ? ' <a href="'. democr()->edit_poll_url($log->qid) .'"><span style="color:red;">NEW</span></a>' : '';

				$out[] = '- '. esc_html( $answ->answer ) . $new;
			}

			return implode('<br>', $out );
		}
		elseif( $col == 'date' ){
			$date=strtotime($log->$col);
			return date('Y.m.d H:i:s',$date);
		}
		else
			return isset( $log->$col ) ? $log->$col : print_r( $log, true );
	}

	function get_columns(){
		$columns = array(
			'qid2'     => 'ID',
			'qid'     => 'Pytanie',
			'aids'    => 'Odpowiedź',
			'date'    => 'Data',			
			'ip_info' => 'Geolokalizacja IP',
		);
		return $columns;
	}
	function get_sortable_columns(){
		return array(
			'qid2'    => array('qid2','asc'),
			'ip_info' => array('ip_info','asc'),
			'qid'     => array('qid','desc'),
			'userid'  => array('userid','asc'),
			'date'    => array('date','desc'),
		);
	}
    function prepare_items() {
		global $wpdb; //This is used only if making any database queries

        $per_page = 20;

/***************************************************************************************************************************************/
		// Строим запрос
		// where --- filters ----
		$where   = 'WHERE 1';
		if( $this->user_id) $where .= ' AND userid = ' . intval($this->user_id);
		if( $ip = @ $_GET['ip'] )   $where .= $wpdb->prepare(' AND ip = %s', $ip );
		$where.=' and open=0 ';

		// новые ответы
		if( @ $_GET['filter'] === 'new_answers' ){
			// ID new ответов
			if( $aqids = $wpdb->get_results("SELECT DISTINCT aid, qid FROM $wpdb->democracy_a WHERE added_by LIKE '%-new'") )
				$where .= " AND qid IN (". implode(',', wp_list_pluck($aqids,'qid')) .") AND ( aids RLIKE '(^|,)(". implode('|', wp_list_pluck($aqids,'aid')) .")(,|$)' )";
			else
				$where .= ' AND 0 ';
		}

		// пагинация
		$this->set_pagination_args( array(
			'total_items' => $wpdb->get_var("SELECT count(*) FROM $wpdb->democracy_log l join $wpdb->democracy_q q on l.qid=q.id $where"),
			'per_page'    => $per_page,
		) );
		$cur_page = (int) $this->get_pagenum(); // после set_pagination_args()

		// orderby offset
		$OFFSET   = 'LIMIT '. ( ($cur_page-1) * $per_page .','. $per_page );
		$order    = (@ strtolower($_GET['order'])=='asc') ? 'ASC' : 'DESC';
		$orderby  = @ $_GET['orderby'] ?: 'date';
		$ORDER_BY = $orderby ? sprintf("ORDER BY %s %s", sanitize_key($orderby), $order ) : '';

		// выполняем запрос
		$sql = "SELECT  logid,ip,qid, qid as qid2, aids,userid,date,expire,ip_info  FROM $wpdb->democracy_log l JOIN $wpdb->democracy_q q on l.qid=q.id $where $ORDER_BY $OFFSET";
		$data=$wpdb->get_results( $sql );
/***************************************************************************************************************************************/
        
        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();
        
        $this->_column_headers = array($columns, $hidden, $sortable);
        
        /**
         * Optional. You can handle your bulk actions however you see fit. In this
         * case, we'll handle them within our package just to keep things clean.
         */
        $this->process_bulk_action();
        
        
        $current_page = $this->get_pagenum();
        
        $total_items = count($data);
        
        $data = array_slice($data,(($current_page-1)*$per_page),$per_page);
        
        $this->items = $data;

        $this->set_pagination_args( array(
            'total_items' => $total_items,                  //WE have to calculate the total number of items
            'per_page'    => $per_page,                     //WE have to determine how many items to show on a page
            'total_pages' => ceil($total_items/$per_page)   //WE have to calculate the total number of pages
        ) );
    }

}