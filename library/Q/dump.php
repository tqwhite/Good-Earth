<?php
/**
 * Write out human readable and PHP includable data.  Keep the data
 * types the same, so null and false and 0 and '' are all different.
 * Something like an improved var_dump() or a neater var_export().
 *
 * Unlike the built-in functions, this does write out null, 0, '', and
 * false appropriately.  Think of it as a data pretty-printer or a data
 * formatter for writing include files.  It even detects and handles
 * circular references.
 *
 * Of course, it would be nice if it could dump the recursive variables
 * so that running the code would set the pointers up, but until I think of
 * an extra-clever way ....
 *
 * function dump($data, $name = false, $html = true, $echo_out = true)
 *   $data = What you want to dump
 *   $name = The name to display, great for naming the dumped data
 *   $html = Escape for HTML, turn off to make an include file
 *   $echo_out = Write to output, turn off if you want the content returned
 *
 * Also includes dump_callstack() to show where you are.
 */


/**
 * Here is some test code.  To enable, add a slash on the next line.
 *
 *
 * $a = array('int' => 0, 'boolean' => true, 'double' => 1.2345, 'string' => '');
 * dump($a, '$a');
 *
 * $b = array('array' => &$a, 'null' => null, 123 => 456);
 * dump($b, '$b');
 *
 * $fp = fopen('dump.php', 'r');
 * $c = array($b, 'file' => $fp);
 * $a[] = &$c;
 * $c[] = 'another thing';
 * dump($c, 'recursive');
 *
 * $type_tests = array(
 * array(0, 'zero'),  // integer
 * array(null, 'null'),  // string
 * array('', 'empty string'),  // string
 * array(false, 'false'),  // integer
 * array(1.2345, 'float'),  // integer
 * array(1.999, 'float'),  // integer
 * );
 *
 * foreach ($type_tests as $v) {
 * $d = array($v[0] => $v[1]);
 * dump($d, 'type_test_' . $v[1]);
 * }
 * exit();
 *
/**
 * End of test code.
 */


/**
 * Dumps data - Calls the __dump_anything() function for the real work
 */
function dump($data, $name = false, $html = null, $echo_out = true) {
	// Create a unique-ish ID
	$uniqId = md5(uniqid(mt_rand(), true));

	if ($name) {
		$uniqStack = array(
			array(
				'base',
				$name
			)
		);
	} else {
		$uniqStack = array(
			array(
				'base',
				'_BASE'
			)
		);
	}

	// If $html is null, determine if this is a web-based request
	if ($html === null) {
		if (php_sapi_name() != 'cli') {
			// Web request
			$html = true;
		} else {
			// Command-line execution
			$html = false;
		}
	}

	// Capture output if desired
	if (! $echo_out) {
		ob_start();
	}

	// HTML
	if ($html) {
		echo '<pre style="text-align: left;font-size:14pt;border-top:1pt solid gray;border-bottom:1pt solid gray; border-left:1pt dashed gray;margin-left:15px;padding-left:5px;">';
	}
	else{
		echo "\n\n----------\n\n";
	}

	// Name
	if ($name !== false) {
		if ($html) {
			echo '<b>' . htmlspecialchars($name) . '</b> = ';
		} else {
			echo $name . ' = ';
		}
	}

	__dump_anything($data, $html, 0, $uniqId, $uniqStack, false);

	if ($name !== false) {
		echo ";\n";
	} else {
		echo "\n";
	}

	if ($html) {
		echo '</pre>';
	}

	if (! $echo_out) {
		$c = ob_get_clean();
		return $c;
	}
	else{
		echo "\n----------\n\n";
	}


	return false;
}


/**
 * Determines how to write the data - calls __dump_* helper functions
 */
function __dump_anything(&$data, $html, $indentation, $uniqId, $uniqStack, $noFormat) {
	$data_type = gettype($data);
	$function = '__dump_' . $data_type;

	if (! function_exists($function)) {
		$function = '__dump_default';
	}

	$function($data, $html, $indentation, $uniqId, $uniqStack, $noFormat);
}


/**
 * Writes out an array, avoids recursion.
 */
function __dump_array(&$data, $html, $indentation, $uniqId, $uniqStack, $noFormat) {
	if (isset($data[$uniqId])) {
		__dump_recursive($data[$uniqId], $html, $noFormat);
		return;
	}

	$data[$uniqId] = $uniqStack;
	echo 'array(';
	__dump_collapse_start($html, $noFormat, count($data) - 1);
	$indentation ++;
	$isFirst = true;

	foreach ($data as $k => &$d) {
		if ($k !== $uniqId) {
			if (! $isFirst) {
				echo ',';
			}

			$isFirst = false;
			echo "\n" . str_repeat("\t", $indentation);
			__dump_index($k, $html);

			if ($html) {
				if ($noFormat) {
					echo ' =&gt; ';
				} else {
					echo ' <font color="#888a85">=&gt;</font> ';
				}
			} else {
				echo ' => ';
			}

			array_push($uniqStack, array(
					'array', &$k
				));
			__dump_anything($d, $html, $indentation, $uniqId, $uniqStack, $noFormat);
			array_pop($uniqStack);
		}
	}

	unset($data[$uniqId]);
	$indentation --;

	if (count($data)) {
		echo "\n" . str_repeat("\t", $indentation);
	}

	__dump_collapse_end($html, $noFormat);
	echo ')';
}


/**
 * Starts a toggle so the user can show/hide large amounts of data
 */
function __dump_collapse_start($html, $noFormat, $count) {
	static $spanId = 0;

	if (! $html || $noFormat) {
		return;
	}

	if ($spanId == 0) {

		?><script language="JavaScript"><!--
function togC(which) {
	var e = document.getElementById('togC' + which);
	if (e) {
		if (e.style.display == 'none') {
			e.style.display = 'inline';
		} else {
			e.style.display = 'none';
		}
	}
	return false;
}
// --></script><?php
	}

	echo ' <a href="#" style="text-decoration:none" onclick="return togC(' . $spanId . ')">/*' . $count . '*/</a> <span id="togC' . $spanId . '">';
	$spanId ++;
}

/**
 * Closes a toggle
 */
function __dump_collapse_end($html, $noFormat) {
	if (! $html || $noFormat) {
		return;
	}

	echo '</span>';
}

/**
 * Write out an index to an array or object.
 *
 * Only called from __dump_array, __dump_object, and __dump_recursive.
 * Only handles known data types (try to keep it to strings and integers).
 */
function __dump_index(&$data, $html) {
	$data_type = gettype($data);
	$function = '__dump_' . $data_type;
	$function($data, $html, 0, false, false, true);
}

/**
 * Writes out an object, avoids recursion.
 */
function __dump_object(&$data, $html, $indentation, $uniqId, $uniqStack, $noFormat) {
	if (isset($data->$uniqId)) {
		$uniqIdInfo = unserialize($data->$uniqId);
		__dump_recursive($uniqIdInfo, $html, $noFormat);
		return;
	}

	$data->$uniqId = serialize($uniqStack);

	if (get_class($data) == 'DOMDocument') {
		echo get_class($data) . '::loadXML(';
		$s = $data->saveXML();
		__dump_anything($s, $html, $indentation, $uniqId, $uniqStack, $noFormat);
		echo ')';
		unset($data->$uniqId);
		return;
	}

	echo get_class($data) . '::__set_state(array(';
	$variables = (array)$data;
	__dump_collapse_start($html, $noFormat, count($variables) - 1);
	$indentation ++;
	$isFirst = true;

	foreach ($variables as $k => $d) {
		if ($k !== $uniqId) {
			if (! $isFirst) {
				echo ',';
			}

			$isFirst = false;
			echo "\n" . str_repeat("\t", $indentation);
			$var_type = 0;  // 0 = public, 1 = protected, 2 = private
			$key_split = explode("\0", $k);

			if (count($key_split) == 3) {
				$k = $key_split[2];

				if ($key_split[1] == '*') {
					$var_type = 1;  // Key:  \0*\0NameOfVariable
				} else {
					$var_type = 2;  // Key:  \0ClassName\*NameOfVariable
				}
			}

			if ($html && ! $noFormat) {
				if ($var_type == 2) {
					echo '<font color="#cc0000">';
				} elseif ($var_type == 1) {
					echo '<font color="#0000cc">';
				} else {
					echo '<font color="#007700">';
				}
			}

			__dump_index($k, $html);

			if ($html) {
				if ($noFormat) {
					echo ' =&gt; ';
				} else {
					echo '</font> <font color="#888a85">=&gt;</font> ';
				}
			} else {
				echo ' => ';
			}

			array_push($uniqStack, array(
					'object',
					$k
				));
			__dump_anything($d, $html, $indentation, $uniqId, $uniqStack, $noFormat);
			array_pop($uniqStack);
		}
	}

	unset($data->$uniqId);
	$indentation --;

	if (count($data)) {
		echo "\n" . str_repeat("\t", $indentation);
	}

	__dump_collapse_end($html, $noFormat);
	echo '))';
}

/**
 * Writes out a recursion message.
 *
 * Called by __dump_array and __dump_object when recursion is detected.
 */
function __dump_recursive(&$uniqStack, $html, $noFormat) {
	if ($html && ! $noFormat) {
		echo '<font color="#770000">';
	}

	echo 'RECURSIVE(';

	foreach ($uniqStack as $member) {
		switch ($member[0]) {
			case 'object':

				if ($html) {
					echo '-&gt;' . htmlspecialchars($member[1]);
				} else {
					echo '->' . $member[1];
				}

				break;

			case 'array':
				echo '[';
				__dump_index($member[1], $html);
				echo ']';
				break;

			default:

				if ($html) {
					echo htmlspecialchars($member[1]);
				} else {
					echo $member[1];
				}

				break;
		}
	}

	echo ')';

	if ($html && ! $noFormat) {
		echo '</font>';
	}
}

/**
 * Writes out a boolean value
 */
function __dump_boolean(&$data, $html, $indentation, $uniqId, $uniqStack, $noFormat) {
	if ($data) {
		if ($html && ! $noFormat) {
			echo '<font color="#00cc00">true</font>';
		} else {
			echo 'true';
		}
	} else {
		if ($html && ! $noFormat) {
			echo '<font color="#cc0000">false</font>';
		} else {
			echo 'false';
		}
	}
}

/**
 * Writes out an escaped string.
 *
 * To avoid additional overhead if the string is parsed, single quotes are
 * preferred.
 */
function __dump_string(&$data, $html, $indentation, $uniqId, $uniqStack, $noFormat) {
	$data_copy = $data;

	if (preg_match('/[^ -~]/', $data)) {
		// specialer case
		$data_copy = str_replace('\\', '\\\\', $data_copy);
		$data_copy = str_replace('"', '\\"', $data_copy);
		$data_copy = str_replace('$', '\\$', $data_copy);
		$data_copy = str_replace("\r", '\\r', $data_copy);
		$data_copy = str_replace("\n", '\\n', $data_copy);
		$data_copy = str_replace("\t", '\\t', $data_copy);
		$data_copy = str_replace("\v", '\\v', $data_copy);
		$data_copy = str_replace("\t", '\\t', $data_copy);
		$data_copy = str_replace("\t", '\\t', $data_copy);
		$data_copy = preg_replace('/([^ -~])/e', "'\x'.bin2hex(\"\\1\")", $data_copy);
		$data_copy = '"' . $data_copy . '"';
	} else {
		// Simple, no double quote needed
		$data_copy = str_replace('\\', '\\\\', $data_copy);
		$data_copy = str_replace('\'', '\\\'', $data_copy);
		$data_copy = '\'' . $data_copy . '\'';
	}

	if ($html) {
		$data_copy = htmlspecialchars($data_copy);

		if (! $noFormat) {
			$sql = '';

			if (__dump_sql_test($data)) {
				$sql = __dump_sql($data);
			}

			$data_copy = $sql . '<font color="#0000cc">' . $data_copy . '</font>';
		}
	}

	echo $data_copy;
}

/**
 * Writes out an integer by calling __dump_double
 */
function __dump_integer(&$data, $html, $indentation, $uniqId, $uniqStack, $noFormat) {
	__dump_double($data, $html, $indentation, $uniqId, $uniqStack, $noFormat);
}

/**
 * Writes out a number.  No special formatting needed.
 */
function __dump_double(&$data, $html, $indentation, $uniqId, $uniqStack, $noFormat) {
	if ($html && ! $noFormat) {
		echo '<font color="#777700">' . $data . '</font>';
	} else {
		echo $data;
	}
}

/**
 * Writes out a null value
 */
function __dump_NULL(&$data, $html, $indentation, $uniqId, $uniqStack, $noFormat) {
	echo 'null';
}

/**
 * Writes out a resource
 *
 * If it is a stream resource, additional attributes are also shown.
 */
function __dump_resource(&$data, $html, $indentation, $uniqId, $uniqStack, $noFormat) {
	$res_type = get_resource_type($data);
	$out = 'null /* resource(' . intval($data) . ', ' . $res_type;

	if ($res_type == 'stream') {
		$meta = stream_get_meta_data($data);
		$out .= ', "' . $meta[uri] . '", "' . $meta[mode] . '"';
	}

	$out .= ') */';

	if ($html) {
		echo htmlspecialchars($out);
	} else {
		echo $out;
	}
}

/**
 * This is the default catch-all that should never get used.
 */
function __dump_default(&$data, $html, $indentation, $uniqId, $uniqStack, $noFormat) {
	echo 'DUMPING TYPE OF ' . gettype($data) . ' NOT SUPPORTED';
}

/**
 * Determine if we should allow the display of this statement as SQL
 */
function __dump_sql_test(&$data) {
	/* Match on any of the following patterns:
	 * select _multiple_ from
	 * inset into _multiple_ values
	 * update _table_ set
	 * alter table
	 */
	if (preg_match('/^(select\s.*\sfrom|insert\s+into\s.*\svalues|update\s+[^\s]+\sset|alter\s+table)\s/i', $data)) {
		return true;
	}

	return false;
}

/**
 * Write out JS to show the select statement in a new window.
 * Do not pass $data by reference for this function.
 */
function __dump_sql($data) {
	$data = trim($data);
	$dataPos = 0;
	$dataLen = strlen($data);
	$out = '';
	$token = '';
	$indent = 1;

	// Chew through $data
	while ($dataPos < $dataLen) {
		$c = substr($data, $dataPos, 1);

		if (preg_match('/[a-zA-Z0-9]/', $c)) {
			$token .= $c;
		} elseif ($c == '\'' || $c == '"' || $c == '`') {
			if ($token != '') {
				$out .= __dump_sql_token($token, $indent);
				$token = '';
			}

			// Find matching quote
			$startPos = $dataPos;
			$dataPos ++;

			while (($dataPos < $dataLen) && ($cMatch = substr($data, $dataPos, 1)) != $c) {
				if ($cMatch == '\\') {
					$dataPos ++;
				}

				$dataPos ++;
			}

			$out .= substr($data, $startPos, 1 + $dataPos - $startPos);
		} else {
			if ($token != '') {
				$out .= __dump_sql_token($token, $indent);
				$token = '';
			}

			$out .= $c;

			if ($c == ',') {
				$out .= "\n" . str_repeat("\t", $indent);
				$c = substr($data, $dataPos + 1, 1);

				while ($c == ' ' || $c == "\t" || $c == "\r" || $c == "\t") {
					$dataPos ++;
					$c = substr($data, $dataPos + 1, 1);
				}
			} elseif ($c == '(') {
				$indent ++;
			} elseif ($c == ')') {
				$indent --;
			}
		}

		$dataPos ++;
	}

	if ($token != '') {
		$out .= __dump_sql_token($token, $indent);
		$token = '';
	}

	$out = trim($out);
	return __dump_sql_html($out);
}


function __dump_sql_token($token, $indent) {
	static $lastTokenBr = false;
	$tokenUpper = strtoupper($token);
	$caps = array(
		'AS',
		'INTO',
		'NULL',
		'ON'
	);
	$br = array(
		'ALTER',
		'FROM',
		'GROUP',
		'HAVING',
		'INNER',
		'INSERT',
		'JOIN',
		'LEFT',
		'LIMIT',
		'ON',
		'ORDER',
		'RIGHT',
		'SELECT',
		'SET',
		'UPDATE',
		'VALUES',
		'WHERE'
	);
	$brIndent = array(
		'AND'
	);

	if (in_array($tokenUpper, $caps)) {
		$lastTokenBr = false;
		return $tokenUpper;
	}

	if (in_array($tokenUpper, $br)) {
		if ($lastTokenBr) {
			return $tokenUpper;
		}

		$lastTokenBr = true;
		return "\n" . str_repeat("\t", $indent - 1) . $tokenUpper;
	}

	if (in_array($tokenUpper, $brIndent)) {
		if ($lastTokenBr) {
			return $tokenUpper;
		}

		$lastTokenBr = true;
		return "\n" . str_repeat("\t", $indent) . $tokenUpper;
	}

	$lastTokenBr = false;
	return $token;
}


function __dump_sql_html(&$sql) {
	$stack = debug_backtrace();

	if (! isset($GLOBALS['__debug_sql_counter'])) {
		$GLOBALS['__debug_sql_counter'] = 1;
		$id = 1;
	} else {
		$id = ++ $GLOBALS['__debug_sql_counter'];
	}

	ob_start();

	?><script type='text/javascript' language='JavaScript'><?php

	if ($id == 1) { ?>
function debugSqlWindow(t) {
	var h = "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">\n";
	h += "<html xmlns=\"http://www.w3.org/1999/xhtml\" xml:lang=\"en\" lang=\"en\">\n";
	h += "<head><title>SQL Statement Print Box</title><head><body>\n";
	h += "<div style=\"border: 2px outset blue; padding: 5px; background-color: #c7e0ff;\">\n";
	h += t;
	h += "</div><br>\n";
	h += "<form><p align=\"center\"><input type=\"button\" value=\"Close\" onClick=\"window.close();\" /></p></form></body></html>\n";

	var w = window.open('', 'newWin', 'width=1000,height=500,scrollbars=yes');
	w.document.write(h);
	w.document.close();
	w.focus();
	return false;
}
<?php
	} ?>
var debugText<?php echo $id; ?> = "<?php

	$out = htmlspecialchars($sql);
	$out = addslashes($out);
	$out = str_replace("\n", '<br>\\n', $out);
	$out = str_replace("\t", ' &nbsp; &nbsp; &nbsp; &nbsp;', $out);
	echo $out;

	?>";
</script><a href="#" onClick="return debugSqlWindow(debugText<?php echo $id; ?>);">/*Show Window*/</a> <?php return ob_get_clean();
}

/**
 * Dump the call stack to the current point.
 *
 * $return = false (wrap in HTML and echo), or true (return text)
 */
function dump_callstack($return = false) {
	$out = array();

	foreach (debug_backtrace() as $trace) {
		if (isset($trace['file'])) {
			$out[] = $trace['file'] . ', line ' . $trace['line'];
		}

		$str = "\t";

		if (isset($trace['type'])) {
			$str .= $trace['class'] . $trace['type'];
		}

		$str .= $trace['function'] . '(';
		$comma = '';

		if (is_array($trace['args'])) {
			foreach ($trace['args'] as $arg) {
				$str .= $comma;
				$comma = ', ';

				switch (gettype($arg)) {
					case 'array':
						$str .= '(array)';
						break;

					case 'object':
						$str .= get_class($arg);
						break;

					case 'boolean':

						if ($arg) {
							$str .= 'true';
						} else {
							$str .= 'false';
						}

						break;

					case 'string':
						$str .= '"' . addslashes($arg) . '"';
						break;

					case 'integer':
					case 'double':
						$str .= $arg;
						break;

					case 'NULL':
						$str .= 'NULL';
						break;

					case 'resource':
						$str .= '(resource)';
						break;

					default:
						$str .= '(unknown type: ' . gettype($arg) . ')';
				}
			}
		}

		$str .= ')';
		$out[] = $str;
	}

	if ($return) {
		// Let's not include the call to dump_callstack()
		array_shift($out);
		array_shift($out);
		return implode("\n", $out);
	}

	echo '<pre>';

	foreach ($out as $o) {
		echo htmlspecialchars($o) . "\n";
	}
	dump_callstack();
	echo '</pre>';
}

