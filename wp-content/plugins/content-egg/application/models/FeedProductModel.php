<?php

namespace ContentEgg\application\models;

/**
 * FeedProductModel class file
 *
 * @author keywordrush.com <support@keywordrush.com>
 * @link https://www.keywordrush.com
 * @copyright Copyright &copy; 2019 keywordrush.com
 */
abstract class FeedProductModel extends Model {

    public function getDump()
    {
        return "CREATE TABLE " . $this->tableName() . " (
                    id bigint(20) unsigned NOT NULL,
                    stock_status tinyint(1) DEFAULT 0,
                    price float(12,2) DEFAULT NULL,
                    title text,
                    orig_url text,
                    product text,
                    PRIMARY KEY  (id),
                    KEY uid (stock_status),
                    KEY orig_url (orig_url(60)),
                    KEY price (price),
                    FULLTEXT (title)
                    ) $this->charset_collate;";
    }

    public function searchByUrl($url)
    {
        $sql = $this->getDb()->prepare('SELECT * FROM ' . $this->tableName() . ' WHERE orig_url LIKE %s LIMIT 1', $this->getDb()->esc_like($url));
        return $this->getDb()->get_results($sql, \ARRAY_A);
    }

    public function searchByKeyword($keyword, $limit = 10, $options = array())
    {
        $where = '';
        if (!empty($options['price_min']))
            $where = $this->getDb()->prepare('price >= %d', $options['price_min']);

        if (!empty($options['price_max']))
        {
            if ($where)
                $where .= ' AND ';
            $where .= $this->getDb()->prepare('price <= %d', $options['price_max']);
        }
        if ($where)
            $where = ' AND ' . $where;

        $sql = $this->getDb()->prepare('SELECT * FROM ' . $this->tableName() . ' WHERE MATCH (title) AGAINST (%s)' . $where . ' LIMIT %d', $keyword, $limit);
        return $this->getDb()->get_results($sql, \ARRAY_A);
    }

    public function searchById($id)
    {
        $sql = $this->getDb()->prepare('SELECT * FROM ' . $this->tableName() . ' WHERE id = %s LIMIT 1', $id);
        return $this->getDb()->get_row($sql, \ARRAY_A);
    }

}
