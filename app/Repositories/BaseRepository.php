<?php

namespace App\Repositories;

use App\Constants\FiltersTypesConstants;
use Illuminate\Database\DatabaseManager;
use Uuid\Uuid;

abstract class BaseRepository
{
    protected $table;
    protected $db;
    protected $uuid;

    /**
     * constructor
     * @param DatabaseManager $db
     * @param Uuid $uuid
     * @return void
     */
    public function __construct(
        DatabaseManager $db,
        Uuid $uuid
    ) {
        $this->db = $db;
        $this->uuid = $uuid;
    }

    /**
     * get data by Id
     * @param string $id
     * @return object
     */
    public function getById(
        string $id
    ) {
        return $this->db->table($this->table)
            ->whereNull('deleted')
            ->find($id);
    }

    /**
     * get dead data by id
     * @param string $id
     * @return object
     */
    public function getDeadById(
        string $id
    ) {
        return $this->db->table($this->table)
            ->whereNotNull('deleted')
            ->find($id);
    }

    /**
     * get data list
     * @param array $fields
     * @param string $order
     * @param string $class
     * @param array|null $filters
     * @param array $query
     * @return array
     */
    public function getList(
        array $fields,
        string $order,
        string $class,
        ? array $filters,
        array $query
    ) : array {
        $list = $this->db->table($this->table)
            ->select($fields)
            ->whereNull('deleted');

        $list = $this->setFilters($list, $filters);

        $list = $list->orderBy($order, $class)
            ->paginate(25);

        $list->appends($query)
            ->links();

        return $list->toArray();
    }

    /**
     * get dead data list
     * @param array $fields
     * @param string $order
     * @param string $class
     * @param array|null $filters
     * @param array $query
     * @return array
     */
    public function getDeadList(
        array $fields,
        string $order,
        string $class,
        ? array $filters,
        array $query
    ) : array {
        $list = $this->db->table($this->table)
            ->select($fields)
            ->whereNotNull('deleted');

        $list = $this->setFilters($list, $filters);
        
        $list = $list->orderBy($order, $class)
            ->paginate(25);

        $list->appends($query)
            ->links();

        return $list->toArray();
    }

    /**
     * get bulk list
     * @param string $id
     * @param array $fields
     * @param string $order
     * @param string $class
     * @param array $query
     * @return array
     */
    public function getBulk(
        array $ids,
        array $fields,
        string $order,
        string $class,
        array $query
    ) : array {
        $list = $this->db->table($this->table)
            ->select($fields)
            ->whereNull('deleted')
            ->whereIn('id', $ids);

        $list = $list->orderBy($order, $class)
            ->paginate(25);

        $list->appends($query)
            ->links();

        return $list->toArray();
    }

    /**
     * insert data
     * @param array $data
     * @return string
     */
    public function insert(
        array $data
    ) : string {
        $id = $this->uuid->uuid4();

        $data['id'] = $id;
        $data['created'] = date('Y-m-d H:i:d');
        $data['modified'] = date('Y-m-d H:i:d');

        $this->db->table($this->table)
            ->insert($data);
        return $id;
    }

    /**
     * update data
     * @param array $data
     * @param string $id
     * @return bool
     */
    public function update(
        array $data,
        string $id
    ) : bool {
        $data['modified'] = date('Y-m-d H:i:d');
        $this->db->table($this->table)
            ->where('id', $id)
            ->whereNull('deleted')
            ->update($data);
        return true;
    }

    /**
     * delete data
     * @param string $id
     * @return bool
     */
    public function delete(
        string $id
    ) : bool {
        $data = [];
        $data['modified'] = date('Y-m-d H:i:d');
        $data['deleted'] = date('Y-m-d H:i:d');
        $this->db->table($this->table)
            ->where('id', $id)
            ->whereNull('deleted')
            ->update($data);
        return true;
    }

    /**
     * set filters before list
     * @param DatabaseManager $list
     * @param array|null $filters
     * @return DatabaseManager
     */
    private function setFilters(
        $list,
        ? array $filters
    ) {
        if (empty($filters)) {
            return $list;
        }
        foreach ($filters as $key => $filter) {
            $map = FiltersTypesConstants::FILTER_TYPE_MAP[$filter['type']];
            switch ($map['action']) {
                case FiltersTypesConstants::ACTION_WHERE:
                    $list->where($key, $map['signal'], $filter['data']);
                    break;
                case FiltersTypesConstants::ACTION_WHERE_LIKE:
                    $data = '%'.$filter['data'].'%';
                    $list->where($key, $map['signal'], $data);
                    break;
                case FiltersTypesConstants::ACTION_WHERE_NULL:
                    $list->whereNull($key);
                    break;
                case FiltersTypesConstants::ACTION_WHERE_NOT_NULL:
                    $list->whereNotNull($key);
                    break;
                default:
                    break;
            }
        }
        return $list;
    }
}
