<?php

namespace Scrollio\Model;

abstract class AbstractModel extends \Geppetto\Object
{
	public function delete($hard_delete = false)
	{
		// Make sure that the record is currently saved
		if (!isset($this->record[$this->primary_key]))
		{
			return;	// Nothing to do
		}

		if ($hard_delete)
		{
			parent::delete($hard_delete);
		}
		else if (array_key_exists('status', $this->schema) && array_key_exists('status', $this->record))
		{
			if (array_key_exists('deleted', $this->schema) && array_key_exists('deleted', $this->record))
			{
				$this->__set('deleted', date('c', time()));
			}

			$this->__set('status', 'f');
			$this->save();
		}
	}
}
