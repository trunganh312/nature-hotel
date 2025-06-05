<?php 

namespace src\Libs\Database;

use Dibi\IDataSource;
use Dibi\Result;
use Dibi\Row;
use Dibi\Connection;
use Dibi\ResultIterator;
use Dibi\Fluent;
use Dibi\Helpers;
use src\Facades\DB;

/**
 * Default implementation of IDataSource.
 */
class ExtraDataSource implements IDataSource
{
	protected Connection $connection;
	protected string $sql;
	protected ?Result $result = null;
	protected ?int $count = null;
	protected ?int $totalCount = null;
	protected array $cols = [];
	protected array $sorting = [];
	protected array $conds = [];
	protected array $joins = [];
	protected array $left_joins = [];
	protected array $where_in = [];
	protected array $where_notin = [];
	protected ?int $offset = null;
	protected ?int $limit = null;

	/**
	 * @param  string  $sql  command or table or view name, as data source
	 */
	public function __construct(string $sql, Connection $connection)
	{
		$this->sql = strpbrk($sql, " \t\r\n") === false
			? $connection->getDriver()->escapeIdentifier($sql) // table name
			: '(' . $sql . ') t'; // SQL command
		$this->connection = $connection;
	}

	/**
	 * Selects columns to query.
	 * @param  string|array  $col  column name or array of column names
	 * @param  string  $as        column alias
	 */
	public function select(string|array $col, ?string $as = null): static
	{
		if (is_array($col)) {
			$this->cols = $col;
		} else {
			$this->cols[$col] = $as;
		}

		$this->result = null;
		return $this;
	}


	/**
	 * Adds conditions to query.
	 */
	public function where($cond): static
	{
		$this->conds[] = is_array($cond)
			? $cond // TODO: not consistent with select and orderBy
			: func_get_args();
		$this->result = $this->count = null;
		return $this;
	}

	public function in($col, $values, $format='') {
		$driver = $this->connection->getDriver();
		
		if (empty($format)) {
			$format = function ($v) use ($driver) {
				return $driver->escapeText($v);
			};
		}
		$col = $driver->escapeIdentifier($col);
		$values = array_map($format, $values);
		$values = implode(',', $values);

		$this->where_in[] = "$col IN ($values)";
		return $this;
	}

    public function join($table, $field_left, $field_right) {
        $table          = $this->connection->getDriver()->escapeIdentifier($table);
        $field_left     = $this->connection->getDriver()->escapeIdentifier($field_left);
        $field_right    = $this->connection->getDriver()->escapeIdentifier($field_right);

        $this->joins[] = "$table ON ($field_left = $field_right)";
        return $this;
    }

    public function leftJoin($table, $field_left, $field_right) {
        $table          = $this->connection->getDriver()->escapeIdentifier($table);
        $field_left     = $this->connection->getDriver()->escapeIdentifier($field_left);
        $field_right    = $this->connection->getDriver()->escapeIdentifier($field_right);

        $this->left_joins[] = "$table ON ($field_left = $field_right)";
        return $this;
    }

	/**
	 * Selects columns to order by.
	 * @param  string|array  $row  column name or array of column names
	 */
	public function orderBy(string|array $row, string $direction = 'ASC'): static
	{
		if (is_array($row)) {
			$this->sorting = $row;
		} else {
			$this->sorting[$row] = $direction;
		}

		$this->result = null;
		return $this;
	}


	/**
	 * Limits number of rows.
	 */
	public function applyLimit(int $limit, ?int $offset = null): static
	{
		$this->limit = $limit;
		$this->offset = $offset;
		$this->result = $this->count = null;
		return $this;
	}


	final public function getConnection(): Connection
	{
		return $this->connection;
	}


	/********************* executing ****************d*g**/


	/**
	 * Returns (and queries) Result.
	 */
	public function getResult(): Result
	{
		if ($this->result === null) {
			$this->result = $this->connection->nativeQuery($this->__toString());
			$this->release();
		}

		return $this->result;
	}


	public function getIterator(): ResultIterator
	{
		return $this->getResult()->getIterator();
	}


	/**
	 * Generates, executes SQL query and fetches the single row.
	 */
	public function fetch(): ?Row
	{
		return $this->getResult()->fetch();
	}


	/**
	 * Like fetch(), but returns only first field.
	 * @return mixed  value on success, null if no next record
	 */
	public function fetchSingle(): mixed
	{
		return $this->getResult()->fetchSingle();
	}


	/**
	 * Fetches all records from table.
	 */
	public function fetchAll(): array
	{
		return $this->getResult()->fetchAll();
	}


	/**
	 * Fetches all records from table and returns associative tree.
	 */
	public function fetchAssoc(string $assoc): array
	{
		return $this->getResult()->fetchAssoc($assoc);
	}


	/**
	 * Fetches all records from table like $key => $value pairs.
	 */
	public function fetchPairs(?string $key = null, ?string $value = null): array
	{
		return $this->getResult()->fetchPairs($key, $value);
	}


	/**
	 * Discards the internal cache.
	 */
	public function release(): void
	{
		$this->count = $this->totalCount = $this->offset = $this->limit = null;
		$this->cols = $this->joins = $this->conds = $this->sorting = $this->left_joins = $this->where_in = $this->where_notin = [];
	}


	/********************* exporting *********************/


	/**
	 * Returns SQL query.
	 */
	public function __toString(): string
	{
		$cols_special = $cols = [];
		foreach ($this->cols as $field => $alias) {
			if (preg_match("/[\(\)]+/", $field)) {
				$cols_special[] = empty($alias) ? $field : "$field as $alias";
			} else {
				$cols[$field] = $alias;
			}
		}

		if (empty($this->cols)) {
			$cols = ['*'];
		}
		$sql = $this->connection->translate(
			"SELECT %ex",
			$cols ? ["%n", $cols] : null,
			"%ex",
			$cols_special && $cols ? ["%SQL",  ','] : null,
			"%ex",
			$cols_special ? ["%SQL", implode(', ', $cols_special)] : null,
			"FROM %SQL",
			$this->sql,
			"%ex",
			$this->joins ? ["INNER JOIN %SQL", implode(' INNER JOIN ', $this->joins)] : null,
			"%ex",
			$this->left_joins ? ["LEFT JOIN %SQL", implode(' LEFT JOIN ', $this->left_joins)] : null,
			"%ex",
			$this->conds || $this->where_in || $this->where_notin ? 'WHERE' : null,
			"%ex",
			$this->conds ? ['%and', $this->conds] : null,
			"%ex",
			$this->conds && ($this->where_in || $this->where_notin) ? 'AND' : null,
			"%ex",
			$this->where_in ? ['%and', $this->where_in] : null,
			"%ex",
			$this->sorting ? ['ORDER BY %by', $this->sorting] : null,
			"%ofs %lmt",
			$this->offset,
			$this->limit,
		);
		$sql = DB::beautifySQL($sql);
		$this->release();
        return $sql;
	}

	public function params() {
		return [
			'table'=> $this->sql,
			'cols'=> $this->cols,
			'joins'=> $this->joins,
			'left_joins'=> $this->left_joins,
			'conds'=> $this->conds,
			'sorting'=> $this->sorting,
			'offset'=> $this->offset,
			'limit'=> $this->limit,
		];
	}

	/********************* counting ****************d*g**/


	/**
	 * Returns the number of rows in a given data source.
	 */
	public function count(): int
	{
		if ($this->count === null) {
			$this->count = $this->conds || $this->offset || $this->limit
				? Helpers::intVal($this->connection->nativeQuery(
					'SELECT COUNT(*) FROM (' . $this->__toString() . ') t',
				)->fetchSingle())
				: $this->getTotalCount();
		}

		return $this->count;
	}


	/**
	 * Returns the number of rows in a given data source.
	 */
	public function getTotalCount(): int
	{
		if ($this->totalCount === null) {
			$this->totalCount = Helpers::intVal($this->connection->nativeQuery(
				'SELECT COUNT(*) FROM ' . $this->sql,
			)->fetchSingle());
		}

		return $this->totalCount;
	}
}
