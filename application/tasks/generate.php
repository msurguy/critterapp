<?php

/**
 * Laravel Generator
 * 
 * Rapidly create models, views, migrations + schema, assets, etc.
 *
 * USAGE:
 * Add this file to your Laravel application/tasks directory
 * and call the methods with: php artisan generate:[model|controller|migration] [args]
 * 
 * See individual methods for additional usage instructions.
 * 
 * @author      Jeffrey Way <jeffrey@jeffrey-way.com>
 * @license     haha - whatever you want.
 * @version     0.8
 * @since       July 26, 2012
 *
 */
class Generate_Task
{

    /*
     * Set these paths to the location of your assets.
     */
    public static $css_dir = 'css/';
    public static $sass_dir  = 'css/sass/';
    public static $less_dir  = 'css/less/';

    public static $js_dir  = 'js/';
    public static $coffee_dir  = 'js/coffee/';

    /*
     * The content for the generate file
     */
    public static $content;


    /**
     * Time Savers
     *
     */
    public function c($args)   { return $this->controller($args); }
    public function m($args)   { return $this->model($args); }
    public function mig($args) { return $this->migration($args); }
    public function v($args)   { return $this->view($args); }
    public function a($args)   { return $this->assets($args); }
    public function t($args)   { return $this->test($args); }
    public function r($args)   { return $this->resource($args); }


    /**
     * Simply echos out some help info.
     *
     */
    public function help() { $this->run(); }
    public function run()
    {
        echo <<<EOT
\n=============GENERATOR COMMANDS=============\n        
generate:controller [name] [methods]
generate:model [name]
generate:view [name]
generate:migration [name] [field:type]
generate:test [name] [methods]
generate:assets [asset]
generate:resource [name] [methods/views]
\n=====================END====================\n
EOT;
    }


    /**
     * Generate a controller file with optional actions.
     *
     * USAGE:
     * 
     * php artisan generate:controller Admin
     * php artisan generate:controller Admin index edit
     * php artisan generate:controller Admin index index:post restful
     * 
     * @param  $args array  
     * @return string
     */
    public function controller($args)
    {
        if ( empty($args) ) {
            echo "Error: Please supply a class name, and your desired methods.\n";
            return;
        }

        // Name of the class and file
        $class_name = ucwords(array_shift($args));

        // Where will this file be stored?
        $file_path = $this->path('controllers') . strtolower("$class_name.php");

        // Begin building up the file's content
        Content::new_class($class_name . '_Controller', 'Base_Controller');

        $content = '';
        // Let's see if they added "restful" anywhere in the args.
        if ( $restful = $this->is_restful($args) ) {
            $args = array_diff($args, array('restful'));
            $content .= 'public $restful = true;';
        }

        // Now we filter through the args, and create the funcs.
        foreach($args as $method) {
            // Were params supplied? Like index:post?
            if ( strpos($method, ':') !== false ) {
                list($method, $verb) = explode(':', $method);
                $content .= Content::func("{$verb}_{$method}");
            } else {
                $action = $restful ? 'get' : 'action';

                $content .= Content::func("{$action}_{$method}");
            }
        }

        // Add methods/actions to class.
        Content::add_after('{', $content);

        // Prettify
        $this->prettify();

        // Create the file
        $this->write_to_file($file_path);
    }


    /**
     * Generate a model file + boilerplate. (To be expanded.)
     *
     * USAGE
     *
     * php artisan generate:model User
     *
     * @param  $args array  
     * @return string
     */
    public function model($args)
    {
        // Name of the class and file
        $class_name = is_array($args) ? ucwords($args[0]) : ucwords($args);

        $file_path = $this->path('models') . strtolower("$class_name.php");

        // Begin building up the file's content
        Content::new_class($class_name, 'Eloquent' );
        $this->prettify();

        // Create the file
        $this->write_to_file($file_path);
    }


    /**
     * Generate a migration file + schema
     *
     * INSTRUCTIONS:
     * - Separate each word with an underscore
     * - Name your migrations according to what you're doing
     * - Try to use the `table` keyword, to hint at the table name: create_users_table
     * - Use the `add`, `create`, `update` and `delete` keywords, according to your needs.
     * - For each field, specify its name and type: id:integer, or body:text
     * - You may also specify additional options, like: age:integer:nullable, or email:string:unique
     *
     *
     * USAGE OPTIONS
     *
     * php artisan generate:migration create_users_table
     * php artisan generate:migration create_users_table id:integer email:string:unique age:integer:nullable
     * php artisan generate:migration add_user_id_to_posts_table user_id:integer
     * php artisan generate:migration delete_active_from_users_table active:boolean
     *
     * @param  $args array  
     * @return string
     */
    public function migration($args)
    {
        if ( empty($args) ) {
            echo "Error: Please provide a name for your migration.\n";
            return;
        }

        $class_name = array_shift($args);

        // Determine what the table name should be.
        $table_name = $this->parse_table_name($class_name);

        // Capitalize where necessary: a_simple_string => A_Simple_String
        $class_name = implode('_', array_map('ucwords', explode('_', $class_name)));

        // Let's create the path to where the migration will be stored.
        $file_path = $this->path('migrations') . date('Y_m_d_His') . strtolower("_$class_name.php");

        $this->generate_migration($class_name, $table_name, $args);

        return $this->write_to_file($file_path);
    }


    /**
     * Create any number of views
     *
     * USAGE:
     *
     * php artisan generate:view home show
     * php artisan generate:view home.index home.show
     *
     * @param $args array
     * @return void
     */
    public function view($paths)
    {
        if ( empty($paths) ) {
            echo "Warning: no views were specified. Add some!\n";
            return;
        }

        foreach( $paths as $path ) {
            $file_path = $this->path('views') . str_replace('.', '/', $path) . '.blade.php';
            self::$content = "This is the $file_path view";
            $this->write_to_file($file_path);
        }
    }


    /**
     * Create assets in the public directory
     *
     * USAGE:
     * php artisan generate:assets style1.css some_module.js
     * 
     * @param  $assets array
     * @return void
     */
    public function assets($assets)
    {
        if( empty($assets) ) {
            echo "Please specify the assets that you would like to create.";
            return;
        }

        foreach( $assets as $asset ) {
            // What type of file? CSS, JS?
            $ext = File::extension($asset);

            if( !$ext ) {
                // Hmm - not sure what to do.
                echo "Warning: Could not determine file type. Please specify an extension.";
                continue;
            }

            // Set the path, dependent upon the file type.
            switch ($ext) {
                case 'js':
                    $path = self::$js_dir . $asset;
                    break;

                case 'coffee':
                    $path = self::$coffee_dir . $asset;
                    break;

                case 'scss':
                case 'sass':
                    $path = self::$sass_dir . $asset;
                    break;

                case 'less':
                    $path = self::$less_dir . $asset;
                    break;

                case 'css':
                default:
                    $path = self::$css_dir . $asset;
                    break;
            }

            $this->write_to_file(path('public') . $path, '');
        }
    }


    /**
     * Create PHPUnit test classes with optional methods
     *
     * USAGE:
     *
     * php artisan generate:test membership
     * php artisan generate:test membership can_disable_user can_reset_user_password
     *
     * @param $args array  
     * @return void
     */
    public function test($args)
    {
        if ( empty($args) ) {
            echo "Please specify a name for your test class.\n";
            return;
        }

        $class_name = ucwords(array_shift($args));

        $file_path = $this->path('tests') . strtolower("{$class_name}.test.php");

        // Begin building up the file's content
        Content::new_class($class_name . '_Test', 'PHPUnit_Framework_TestCase');

        // add the functions
        $tests = '';
        foreach($args as $test) {
            // make lower case
            $tests .= Content::func("test_{$test}");
        }

        // add funcs to class
        Content::add_after('{', $tests);

        // Create the file
        $this->write_to_file($file_path, $this->prettify());
    }


    /**
     * Creates the content for the migration file.
     *
     * @param  $class_name string
     * @param  $table_name string
     * @param  $args array
     * @return void
     */
    protected function generate_migration($class_name, $table_name, $args)
    {
        // Figure out what type of event is occuring. Create, Delete, Add?
        list($table_action, $table_event) = $this->parse_action_type($class_name);

        // Now, we begin creating the contents of the file.
        Content::new_class($class_name);

        /* The Migration Up Function */
        $up = $this->migration_up($table_event, $table_action, $table_name, $args);
       
        /* The Migration Down Function */
        $down = $this->migration_down($table_event, $table_action, $table_name, $args);

        // Add both the up and down function to the migration class.
        Content::add_after('{', $up . $down);

        return $this->prettify();
    }


    protected function migration_up($table_event, $table_action, $table_name, $args)
    {
        $up = Content::func('up');

        // Insert a new schema function into the up function.
        $up = $this->add_after('{', Content::schema($table_action, $table_name), $up);

        // Create the field rules for for the schema
        if ( $table_event === 'create' ) {
            $fields = $this->add_columns($args);
            $fields .= $this->set_column('timestamps', null) . ';';
        }

        else if ( $table_event === 'delete' ) {
            $fields = $this->drop_columns($args);
        }

        else if ( $table_event === 'add' || $table_event === 'update' ) {
            $fields = $this->add_columns($args);
        }

        // Insert the fields into the schema function
        return $this->add_after('function($table) {', $fields, $up);
    }


    protected function migration_down($table_event, $table_action, $table_name, $args)
    {
        $down = Content::func('down');

        if ( $table_event === 'create' ) {
           $schema = Content::schema('drop', $table_name, false);

           // Add drop schema into down function
           $down = $this->add_after('{', $schema, $down);
        } else {
            // for delete, add, and update
            $schema = Content::schema('table', $table_name);
        }

        if ( $table_event === 'delete' ) {
            $fields = $this->add_columns($args);

            // add fields to schema
            $schema = $this->add_after('function($table) {', $fields, $schema);
            
            // add schema to down function
            $down = $this->add_after('{', $schema, $down);
        }

        else if ( $table_event === 'add' ) {
            $fields = $this->drop_columns($args);

            // add fields to schema
            $schema = $this->add_after('function($table) {', $fields, $schema);

            // add schema to down function
            $down = $this->add_after('{', $schema, $down);

        }

        else if ( $table_event === 'update' ) {
            // add schema to down function
            $down = $this->add_after('{', $schema, $down);
        }

        return $down;
    }


    /**
     * Generate resource (model, controller, and views)
     *
     * @param $args array  
     * @return void
     */
    public function resource($args)
    {
        // Pluralize controller name
        if ( !preg_match('/admin|config/', $args[0]) ) {
            $args[0] = Str::plural($args[0]);
        }

        $this->controller($args);

        // Singular for everything else
        $resource_name = Str::singular(array_shift($args));

        if ( $this->is_restful($args) ) {
            // Remove that restful item from the array. No longer needed.
            $args = array_diff($args, array('restful'));
            $args = $this->determine_views($args);
        }

        // Let's take any supplied view names, and set them
        // in the resource name's directory.
        $views = array_map(function($val) use($resource_name) {
            return "{$resource_name}.{$val}";
        }, $args);

        $this->view($views);
        
        $this->model($resource_name);
    }


    /**
     * Figure out what the name of the table is.
     *
     * Fetch the value that comes right before "_table"
     * Or try to grab the very last word that comes after "_" - create_*users*
     * If all else fails, return a generic "TABLE", to be filled in by the user.
     *
     * @param  $class_name string  
     * @return string
     */
    protected function parse_table_name($class_name)
    {
        // Try to figure out the table name
        // We'll use the word that comes immediately before "_table"
        // create_users_table => users
        preg_match('/([a-zA-Z]+)_table/', $class_name, $matches);

        if ( empty($matches) ) {
            // Or, if the user doesn't write "table", we'll just use
            // the text at the end of the string
            // create_users => users
            preg_match('/_([a-zA-Z]+)$/', $class_name, $matches);
        }

        // Hmm - I'm stumped. Just use a generic name.
        return empty($matches)
            ? "TABLE"
            : $matches[1];
    }


    /**
     * Write the contents to the specified file
     *
     * @param  $file_path string
     * @param $content string
     * @param $type string [model|controller|migration]  
     * @return void
     */
    protected function write_to_file($file_path,  $success = '')
    {
        $success = $success ?: "Create: $file_path.\n";

        if ( File::exists($file_path) ) {
            // we don't want to overwrite it
            echo "Warning: File already exists at $file_path\n";
            return;
        }

        // As a precaution, let's see if we need to make the folder.
        File::mkdir(dirname($file_path));

        if ( File::put($file_path, self::$content) !== false ) {
            echo $success;
        } else {
            echo "Whoops - something...erghh...went wrong!\n";
        }
    }


    /**
     * Try to determine what type of table action should occur.
     * Add, Create, Delete??
     *
     * @param  $class_name string  
     * @return aray
     */
    protected function parse_action_type($class_name)
    {
         // What type of action? Creating a table? Adding a column? Deleting?
        if ( preg_match('/delete|update|add(?=_)/i', $class_name, $matches) ) {
            $table_action = 'table';
            $table_event = strtolower($matches[0]);
        } else {
            $table_action = $table_event = 'create';
        }

        return array($table_action, $table_event);
    }


    protected function increment()
    {
        return "\$table->increments('id')";
    }


    protected function set_column($type, $field = '')
    {
        return empty($field)
            ? "\$table->$type()"
            : "\$table->$type('$field')";
    }


    protected function add_option($option)
    {
        return "->{$option}()";
    }


    /**
     * Add columns
     *
     * Filters through the provided args, and builds up the schema text.
     *
     * @param  $args array  
     * @return string
     */
    protected function add_columns($args)
    {
        $content = '';

        // Build up the schema
        foreach( $args as $arg ) {
            // Like age, integer, and nullable
            @list($field, $type, $setting) = explode(':', $arg);

            if ( !$type ) {
                echo "There was an error in your formatting. Please try again. Did you specify both a field and data type for each? age:int\n";
                die();
            }

            // Primary key check
            if ( $field === 'id' and $type === 'integer' ) {
                $rule = $this->increment();
            } else {
                $rule = $this->set_column($type, $field);

                if ( !empty($setting) ) {
                    $rule .= $this->add_option($setting);
                }
            }

            $content .= $rule . ";";
        }

        return $content;
    }


    /**
     * Drop Columns
     *
     * Filters through the args and applies the "drop_column" syntax
     *
     * @param $args array  
     * @return string
     */
    protected function drop_columns($args)
    {
        $fields = array_map(function($val) {
            $bits = explode(':', $val);
            return "'$bits[0]'";
        }, $args);
       
        if ( count($fields) === 1 ) {
            return "\$table->drop_column($fields[0]);";
        } else {
            return "\$table->drop_column(array(" . implode(', ', $fields) . "));";
        }
    }

    public function path($dir)
    {
        return path('app') . "$dir/";
    }


    /**
     * Crazy sloppy prettify. TODO - Cleanup
     *
     * @param  $content string  
     * @return string
     */
    protected function prettify()
    {
        $content = self::$content;

        $content = str_replace('<?php ', "<?php\n\n", $content);
        $content = str_replace('{}', "\n{\n\n}", $content);
        $content = str_replace('public', "\n\n\tpublic", $content);
        $content = str_replace("() \n{\n\n}", "()\n\t{\n\n\t}", $content);
        $content = str_replace('}}', "}\n\n}", $content);

        // Migration-Specific
        $content = preg_replace('/ ?Schema::/', "\n\t\tSchema::", $content);
        $content = preg_replace('/\$table(?!\))/', "\n\t\t\t\$table", $content);
        $content = str_replace('});}', "\n\t\t});\n\t}", $content);
        $content = str_replace(');}', ");\n\t}", $content);
        $content = str_replace("() {", "()\n\t{", $content);

        self::$content = $content;
    }


    public function add_after($where, $to_add, $content)
    {
        return str_replace($where, $where . $to_add, $content);
    }


    protected function is_restful($args)
    {
        $restful_pos = array_search('restful', $args);
        return $restful_pos !== false;
    }


    protected function determine_views($args)
    {
        // Separate index:post, and remove any non-GET views.
        array_walk($args, function(&$arg, $index) use(&$args) {
            // method, optional verb
            $bits = explode(':', $arg);
            $arg = $bits[0];

            if ( isset($bits[1]) && $bits[1] !== 'get' ) {
                // then we shouldn't create a view for it.
                unset($args[$index]);
            }
        });

        return $args;
    }
}

class Content {
    public static function new_class($name, $extends_class = null)
    {
        $content = "<?php class $name";
        if ( !empty($extends_class) ) {
            $content .= " extends $extends_class";
        }

        $content .= ' {}';

        Generate_Task::$content = $content;
    }

    public static function func($func_name)
    {
        return "public function {$func_name}() {}";
    }

    public static function schema($table_action, $table_name, $cb = true)
    {
        $content = "Schema::$table_action('$table_name'";

        return $cb
            ? $content . ', function($table) {});'
            : $content . ');';
    }

    public static function add_after($where, $to_add)
    {
        Generate_Task::$content = str_replace($where, $where . $to_add, Generate_Task::$content);

    }
}