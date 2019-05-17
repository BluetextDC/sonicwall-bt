<?php

/**
 * A condition that is always true.
 *
 * It can be useful in situations where we need to return a condition object but don't want to influence
 * the query.
 *
 * Remember, the first rule of the Tautology Club is the first rule of the Tautology Club!
 *
 * @since 2.5.6
 */
class Toolset_Relationship_Query_Condition_Tautology extends Toolset_Relationship_Query_Condition {

	/**
	 * Get a part of the WHERE clause that applies the condition.
	 *
	 * @return string Valid part of a MySQL query, so that it can be
	 *     used in WHERE ( $condition1 ) AND ( $condition2 ) AND ( $condition3 ) ...
	 */
	public function get_where_clause() {
		return ' 1 = 1 ';
	}
}