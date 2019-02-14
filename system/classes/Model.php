<?php defined('FRTCFTYU') or die('No direct script access.');

/*
 * Main model project. 
 *
*/
class Model {
	
	/*
	 * PDO object. 
	 *
	*/
        protected $_db = 'db';
	
	/*
	 * Autoincrement column. 
	 *
	*/
	protected $_id = 'id';
	
	/*
	 * Global model config. 
	 *
	*/
	protected $_model_config;
        
        /*
	 * Config. 
	 *
	*/
	protected $_config;
	
	/*
	 * Columns table. 
	 *
	*/
	public $_data = array();
        
        /*
	 * Name model. 
	 *
	*/
        static private $_model;
	
	/*
	 * Connection database. 
         * @param $db_name Database name
	 *
	*/
	public function __construct($db_name=null)
	{
                $this->_db = Connection::get($db_name);
                $this->_model_config = Useracc::config('model');
                $this->_config = $this->_config ? Useracc::config($this->_config) : Useracc::config(mb_strtolower(self::$_model));
                $prefix_db = isset($this->_config['prefix_db']) ? $this->_config['prefix_db'] : $this->_model_config['prefix_db'];
                $this->_table = $prefix_db ? $prefix_db .'_'. $this->_table : $this->_table;
	}
	
	/*
	 * Disconnect database. 
	 *
	*/
	public function __destruct()
	{
		$this->_db = null;
	}
	
	/*
	 * Set column object from table. 
	 *
	*/
	public function __set($name, $value)
	{
		$this->_data[$name] = $value;
	}
	
	/*
	 * Get column object. 
	 *
	*/
	public function __get($name)
	{

            if ( ! empty($this->_data) && array_key_exists($name, $this->_data) == TRUE ) {
                return $this->_data[$name];
            }
	}
	
	/*
	 * Factory object modules. 
	 * @param $model Name model new object
	 * @return New object 
	 *
	*/
	public static function factory($model)
	{
            self::$_model = ucfirst($model);
            $model_name = 'Model_' . self::$_model;
            $model = new $model_name();
            return $model;
	}
        
        /*
	 * Select all data. 
	 * @return object
	 *
	*/
        public function get_all()
        {
            $sql = "SELECT * FROM {$this->_table}";

            $stmt = $this->_db->prepare($sql);
            $stmt->execute();

            $this->_data = $stmt->fetchAll();

            return $this;
        }
	
	/*
	 * Select data by condition. 
	 * @param $variable Value from table DB.
	 * @param $condition Condition select.
	 * @param $value Value select
	 * @return object
	 *
	*/
	public function where($variable, $condition, $value)
	{
            $value = Security::is($value);

            $sql = "SELECT * FROM {$this->_table} WHERE `{$variable}` {$condition} :value";

            $stmt = $this->_db->prepare($sql);
            $stmt->bindParam( ':value', $value );
            $stmt->execute();

            $this->_data = $stmt->fetchAll();

            return $this;
	}
	
        /*
	 * Select and where. 
	 * @param $array Массив со значениями.
	 * @return object
	 *
	*/
	public function and_where($array)
	{
            $sql = "SELECT * FROM {$this->_table}";
            foreach($array as $key => $data) {
                if($key == 0) {
                    $sql .= " WHERE `{$data['col']}` {$data['com']} :value_{$key}";
                }
                else {
                    $sql .= " AND `{$data['col']}` {$data['com']} :value_{$key}";
                }
            }
            $stmt = $this->_db->prepare($sql);
            foreach($array as $key => $data) {
                $stmt->bindParam(':value_'.$key, $data['val']);
            }
            $stmt->execute();
            $this->_data = $stmt->fetchAll();
            return $this;
            
	}
	
	/*
	 * Current element model. 
	 * @return object
	 *
	*/
	public function current()
	{
            $this->_data = current($this->_data);
            return $this;
	}
	
	/*
	 * Sorting data model. 
	 * @param $type Type sorting asc or desc.
	 * @param $variable Parameter sorting.
	 * @return object
	 *
	*/
	public function order_by($type, $variable)
	{
		$volume = array();
		
		foreach ($this->_data as $key => $row) {
		
			$volume[$key]  = $row[ $variable ];
		}

		array_multisort($volume, mb_strtolower($type) == 'asc' ? SORT_ASC : SORT_DESC, $this->_data);
		
		return $this;

	}
	
	/*
	 * Insert data in BD. 
	 * @param $data array
	 * @return boolean Result insert data
	 *
	*/
	public function insert($data)
	{
            $variables = NULL;

            $value = NULL;

            foreach ($data as $key => $v) {

                    if ( $variables ) {

                            $variables .= ', '.$key;

                            $value .= ', :'.$key;
                    }
                    else {

                            $variables .= $key;

                            $value .= ':'.$key;
                    }

            }

            if ( ! empty($variables) && ! empty($value)  ) {

                $sql = "INSERT INTO {$this->_table} ( {$variables} ) VALUE ( {$value} )";

                $stmt = $this->_db->prepare($sql);

                foreach ($data as $key => $v) {

                        $stmt->bindParam( $key, $data[$key] );
                }

                $stmt->execute();

                if ($this->_id) {

                        $stmt = $this->_db->prepare( "SELECT MAX(`id`) as `id` FROM {$this->_table}" );

                        $stmt->execute();

                        $result = $stmt->fetch();

                        return $result['id'];
                }
                return TRUE;
            }
            return FALSE;
	}
	
	/*
	 * Update data in BD. 
	 * @return boolean Result insert data
	 *
	*/
	public function update()
	{
            $update = NULL;

            if ($this->_data) {

                    foreach ($this->_data as $key => $item) {

                            if ($key != 'id') {

                                    if ( $update ) {

                                            $update .= ", {$key} = :{$key}";
                                    }
                                    else {

                                            $update .= "{$key} = :{$key}";
                                    }
                            }
                    }

                    if ($update) {

                            $sql = "UPDATE {$this->_table} SET {$update} WHERE `id` = {$this->_data['id']}";

                            $stmt = $this->_db->prepare($sql);

                            foreach ($this->_data as $key => $v) {

                                    if ($key != 'id') {

                                            $stmt->bindParam( $key, $this->_data[$key] );
                                    }
                            }

                            return $stmt->execute();
                    }
            }

            return FALSE;
		
	}
	
	/*
	 * Select data by condition. 
	 * @param $id Element`s id.
	 * @return object
	 *
	*/
	public function delete($id)
	{
		$id = Security::is($id, 'integer');

		$sql = "DELETE FROM {$this->_table} WHERE `id` = :value";
		
		$stmt = $this->_db->prepare($sql);
		$stmt->bindParam( ':value', $id );
		
		return $stmt->execute();
	}
        
        public function count()
        {
            return count($this->_data);
        }
	
	
}