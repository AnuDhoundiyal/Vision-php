```php
<?php
// This file now acts as a wrapper to include the centralized Database class.
require_once __DIR__ . '/../../config/database.php';

// The global $conn variable is now available from config/database.php
// You can still use get_db() if needed for backward compatibility,
// but direct usage of $db = Database::getInstance(); $conn = $db->getConnection(); is preferred.
?>
```