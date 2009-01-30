<?php
/**
* Smarty Internal Plugin Templateparser
*
* This is the template parser.
* It is generated from the internal.templateparser.y file
* @package Smarty
* @subpackage Compiler
* @author Uwe Tews
*/

/**
 * This can be used to store both the string representation of
 * a token, and any useful meta-data associated with the token.
 *
 * meta-data should be stored as an array
 */
class TP_yyToken implements ArrayAccess
{
    public $string = '';
    public $metadata = array();

    function __construct($s, $m = array())
    {
        if ($s instanceof TP_yyToken) {
            $this->string = $s->string;
            $this->metadata = $s->metadata;
        } else {
            $this->string = (string) $s;
            if ($m instanceof TP_yyToken) {
                $this->metadata = $m->metadata;
            } elseif (is_array($m)) {
                $this->metadata = $m;
            }
        }
    }

    function __toString()
    {
        return $this->_string;
    }

    function offsetExists($offset)
    {
        return isset($this->metadata[$offset]);
    }

    function offsetGet($offset)
    {
        return $this->metadata[$offset];
    }

    function offsetSet($offset, $value)
    {
        if ($offset === null) {
            if (isset($value[0])) {
                $x = ($value instanceof TP_yyToken) ?
                    $value->metadata : $value;
                $this->metadata = array_merge($this->metadata, $x);
                return;
            }
            $offset = count($this->metadata);
        }
        if ($value === null) {
            return;
        }
        if ($value instanceof TP_yyToken) {
            if ($value->metadata) {
                $this->metadata[$offset] = $value->metadata;
            }
        } elseif ($value) {
            $this->metadata[$offset] = $value;
        }
    }

    function offsetUnset($offset)
    {
        unset($this->metadata[$offset]);
    }
}

/** The following structure represents a single element of the
 * parser's stack.  Information stored includes:
 *
 *   +  The state number for the parser at this level of the stack.
 *
 *   +  The value of the token stored at this level of the stack.
 *      (In other words, the "major" token.)
 *
 *   +  The semantic value stored at this level of the stack.  This is
 *      the information used by the action routines in the grammar.
 *      It is sometimes called the "minor" token.
 */
class TP_yyStackEntry
{
    public $stateno;       /* The state-number */
    public $major;         /* The major token value.  This is the code
                     ** number for the token at this stack level */
    public $minor; /* The user-supplied minor token value.  This
                     ** is the value of the token  */
};

// code external to the class is included here

// declare_class is output here
#line 12 "internal.templateparser.y"
class Smarty_Internal_Templateparser#line 109 "internal.templateparser.php"
{
/* First off, code is included which follows the "include_class" declaration
** in the input file. */
#line 14 "internal.templateparser.y"

    // states whether the parse was successful or not
    public $successful = true;
    public $retvalue = 0;
    private $lex;
    private $internalError = false;

    function __construct($lex, $compiler) {
        // set instance object
        self::instance($this); 
        $this->lex = $lex;
        $this->smarty = Smarty::instance(); 
        $this->compiler = $compiler;
        $this->template = $this->compiler->template;
        $this->cacher = $this->template->cacher_object; 
				$this->nocache = false;
    }
    public static function &instance($new_instance = null)
    {
        static $instance = null;
        if (isset($new_instance) && is_object($new_instance))
            $instance = $new_instance;
        return $instance;
    }
    
#line 140 "internal.templateparser.php"

/* Next is all token values, as class constants
*/
/* 
** These constants (all generated automatically by the parser generator)
** specify the various kinds of tokens (terminals) that the parser
** understands. 
**
** Each symbol here is a terminal symbol in the grammar.
*/
    const TP_OTHER                          =  1;
    const TP_LDELSLASH                      =  2;
    const TP_RDEL                           =  3;
    const TP_COMMENTSTART                   =  4;
    const TP_COMMENTEND                     =  5;
    const TP_NUMBER                         =  6;
    const TP_MATH                           =  7;
    const TP_UNIMATH                        =  8;
    const TP_INCDEC                         =  9;
    const TP_OPENP                          = 10;
    const TP_CLOSEP                         = 11;
    const TP_OPENB                          = 12;
    const TP_CLOSEB                         = 13;
    const TP_DOLLAR                         = 14;
    const TP_DOT                            = 15;
    const TP_COMMA                          = 16;
    const TP_COLON                          = 17;
    const TP_SEMICOLON                      = 18;
    const TP_VERT                           = 19;
    const TP_EQUAL                          = 20;
    const TP_SPACE                          = 21;
    const TP_PTR                            = 22;
    const TP_APTR                           = 23;
    const TP_ID                             = 24;
    const TP_SI_QSTR                        = 25;
    const TP_EQUALS                         = 26;
    const TP_NOTEQUALS                      = 27;
    const TP_GREATERTHAN                    = 28;
    const TP_LESSTHAN                       = 29;
    const TP_GREATEREQUAL                   = 30;
    const TP_LESSEQUAL                      = 31;
    const TP_IDENTITY                       = 32;
    const TP_NONEIDENTITY                   = 33;
    const TP_NOT                            = 34;
    const TP_LAND                           = 35;
    const TP_LOR                            = 36;
    const TP_QUOTE                          = 37;
    const TP_BOOLEAN                        = 38;
    const TP_IN                             = 39;
    const TP_ANDSYM                         = 40;
    const TP_UNDERL                         = 41;
    const TP_BACKTICK                       = 42;
    const TP_AT                             = 43;
    const TP_LITERALSTART                   = 44;
    const TP_LITERALEND                     = 45;
    const TP_LDELIMTAG                      = 46;
    const TP_RDELIMTAG                      = 47;
    const TP_PHP                            = 48;
    const TP_XML                            = 49;
    const TP_LDEL                           = 50;
    const YY_NO_ACTION = 310;
    const YY_ACCEPT_ACTION = 309;
    const YY_ERROR_ACTION = 308;

/* Next are that tables used to determine what action to take based on the
** current state and lookahead token.  These tables are used to implement
** functions that take a state number and lookahead value and return an
** action integer.  
**
** Suppose the action integer is N.  Then the action is determined as
** follows
**
**   0 <= N < self::YYNSTATE                              Shift N.  That is,
**                                                        push the lookahead
**                                                        token onto the stack
**                                                        and goto state N.
**
**   self::YYNSTATE <= N < self::YYNSTATE+self::YYNRULE   Reduce by rule N-YYNSTATE.
**
**   N == self::YYNSTATE+self::YYNRULE                    A syntax error has occurred.
**
**   N == self::YYNSTATE+self::YYNRULE+1                  The parser accepts its
**                                                        input. (and concludes parsing)
**
**   N == self::YYNSTATE+self::YYNRULE+2                  No such action.  Denotes unused
**                                                        slots in the yy_action[] table.
**
** The action table is constructed as a single large static array $yy_action.
** Given state S and lookahead X, the action is computed as
**
**      self::$yy_action[self::$yy_shift_ofst[S] + X ]
**
** If the index value self::$yy_shift_ofst[S]+X is out of range or if the value
** self::$yy_lookahead[self::$yy_shift_ofst[S]+X] is not equal to X or if
** self::$yy_shift_ofst[S] is equal to self::YY_SHIFT_USE_DFLT, it means that
** the action is not in the table and that self::$yy_default[S] should be used instead.  
**
** The formula above is for computing the action when the lookahead is
** a terminal symbol.  If the lookahead is a non-terminal (as occurs after
** a reduce action) then the static $yy_reduce_ofst array is used in place of
** the static $yy_shift_ofst array and self::YY_REDUCE_USE_DFLT is used in place of
** self::YY_SHIFT_USE_DFLT.
**
** The following are the tables generated in this section:
**
**  self::$yy_action        A single table containing all actions.
**  self::$yy_lookahead     A table containing the lookahead for each entry in
**                          yy_action.  Used to detect hash collisions.
**  self::$yy_shift_ofst    For each state, the offset into self::$yy_action for
**                          shifting terminals.
**  self::$yy_reduce_ofst   For each state, the offset into self::$yy_action for
**                          shifting non-terminals after a reduce.
**  self::$yy_default       Default action for each state.
*/
    const YY_SZ_ACTTAB = 495;
static public $yy_action = array(
 /*     0 */   153,  155,  105,  102,  169,  309,   38,  126,  122,  116,
 /*    10 */   118,  154,  159,  110,   89,  162,  153,  155,    8,  182,
 /*    20 */   181,  180,  196,  136,  125,  124,  123,  174,  112,   37,
 /*    30 */   154,  120,  185,   24,   70,   94,   16,   19,  177,  178,
 /*    40 */    40,  179,   86,  132,  192,  183,  153,  155,  167,   24,
 /*    50 */    93,  194,  115,  114,   12,  129,   19,  131,  154,  116,
 /*    60 */   118,   84,  167,   26,  160,  182,  181,  180,  196,  136,
 /*    70 */   125,  124,  123,   13,  185,  103,   25,  103,    5,   24,
 /*    80 */     6,  185,   40,   25,   19,    5,   75,    6,   90,   43,
 /*    90 */   153,  155,   93,  194,  143,   97,  190,   48,   17,   91,
 /*   100 */   194,   21,    4,  145,  103,   26,  160,  141,   34,    4,
 /*   110 */   154,  177,   26,  160,  108,   83,  185,  192,   25,   28,
 /*   120 */    16,  167,    6,   24,   40,   82,  153,  155,  184,  113,
 /*   130 */   169,  116,  118,  157,   93,  194,   19,   44,  188,  137,
 /*   140 */   134,  139,  146,    7,   49,   37,  103,   26,  160,  161,
 /*   150 */    74,   40,  133,   17,  177,  178,   21,  179,   86,   24,
 /*   160 */   192,   20,   11,   36,  167,  154,   13,  223,   62,   99,
 /*   170 */   145,  129,  177,  178,   11,  179,   86,  138,  192,   50,
 /*   180 */    35,   99,  167,  105,  113,    1,  107,   22,  172,  129,
 /*   190 */    80,   19,   37,  153,  155,   30,   17,   65,  167,   21,
 /*   200 */   190,  177,  178,   23,  179,   86,   82,  192,  140,   18,
 /*   210 */   187,  167,   37,   33,  142,  170,  150,   72,  129,  133,
 /*   220 */    17,  177,  178,   21,  179,   86,   24,  192,   20,  189,
 /*   230 */    54,  167,  154,  153,  155,  153,  155,   10,  129,  177,
 /*   240 */   166,   59,  179,   86,    9,  192,  185,  117,   25,  167,
 /*   250 */    16,  113,    6,  186,   41,   87,  151,   20,   19,   92,
 /*   260 */   165,  154,   47,  176,   29,  194,   24,   88,   24,    3,
 /*   270 */    52,  116,  118,   17,  135,  193,   21,   26,  160,  177,
 /*   280 */   178,  103,  179,   86,  145,  192,   96,   19,   52,  167,
 /*   290 */    10,  119,   17,  103,  106,   21,   52,  177,  178,  150,
 /*   300 */   179,   86,   67,  192,   54,  177,  178,  167,  179,   86,
 /*   310 */   109,  192,  164,  177,  166,  167,  179,   86,  163,  192,
 /*   320 */   111,  177,   11,  167,   77,   81,  170,  192,   32,   99,
 /*   330 */   101,  167,   15,  133,  175,  135,   57,  177,  178,   27,
 /*   340 */   179,   86,   78,  192,  170,  177,  178,  167,  179,   86,
 /*   350 */   177,  192,   68,  157,   85,  167,  192,  153,  155,  172,
 /*   360 */   167,  177,  178,   60,  179,   86,   71,  192,  170,  167,
 /*   370 */   149,  167,  177,  178,   64,  179,   86,   31,  192,  153,
 /*   380 */   155,  156,  167,  177,  178,  171,  179,   86,   63,  192,
 /*   390 */    24,  157,   76,  167,  127,  122,   66,  177,  178,  148,
 /*   400 */   179,   86,  190,  192,   56,  177,  178,  167,  179,   86,
 /*   410 */    46,  192,   24,  177,  178,  167,  179,   86,  100,  192,
 /*   420 */    61,   51,   39,  167,   73,   23,  168,  128,  167,  177,
 /*   430 */   178,   58,  179,   86,  190,  192,  157,  121,   14,  167,
 /*   440 */   177,  178,   53,  179,   86,  188,  192,    6,  183,   40,
 /*   450 */   167,  177,  178,   69,  179,   86,   55,  192,   40,  173,
 /*   460 */    79,  167,   95,  191,  147,  177,  178,  157,  179,   86,
 /*   470 */    98,  192,   45,  104,   15,  167,   23,  144,  150,   40,
 /*   480 */     2,  195,  130,  186,  158,  152,   50,   42,  103,  203,
 /*   490 */   203,  203,  203,  203,   22,
    );
    static public $yy_lookahead = array(
 /*     0 */     7,    8,   19,   14,   11,   52,   53,   54,   55,   35,
 /*    10 */    36,   24,    3,   24,   60,   13,    7,    8,   16,   26,
 /*    20 */    27,   28,   29,   30,   31,   32,   33,    3,   41,   57,
 /*    30 */    24,   59,    6,   40,   62,   63,   10,   50,   66,   67,
 /*    40 */    14,   69,   70,    9,   72,   82,    7,    8,   76,   40,
 /*    50 */    24,   25,   66,   67,   20,   83,   50,    3,   24,   35,
 /*    60 */    36,   61,   76,   37,   38,   26,   27,   28,   29,   30,
 /*    70 */    31,   32,   33,   17,    6,   21,    8,   21,   10,   40,
 /*    80 */    12,    6,   14,    8,   50,   10,   58,   12,   60,   14,
 /*    90 */     7,    8,   24,   25,    1,    2,   68,    4,   12,   24,
 /*   100 */    25,   15,   34,    1,   21,   37,   38,    5,   61,   34,
 /*   110 */    24,   66,   37,   38,   18,   70,    6,   72,    8,   64,
 /*   120 */    10,   76,   12,   40,   14,   22,    7,    8,    3,   43,
 /*   130 */    11,   35,   36,   78,   24,   25,   50,   44,    1,   46,
 /*   140 */    47,   48,   49,   50,   14,   57,   21,   37,   38,   80,
 /*   150 */    62,   14,   50,   12,   66,   67,   15,   69,   70,   40,
 /*   160 */    72,   20,   10,   57,   76,   24,   17,    3,   62,   17,
 /*   170 */     1,   83,   66,   67,   10,   69,   70,   59,   72,   42,
 /*   180 */    39,   17,   76,   19,   43,   21,   22,   50,   66,   83,
 /*   190 */    58,   50,   57,    7,    8,   73,   12,   62,   76,   15,
 /*   200 */    68,   66,   67,   71,   69,   70,   22,   72,    3,   23,
 /*   210 */    88,   76,   57,   75,   45,   77,   78,   62,   83,   50,
 /*   220 */    12,   66,   67,   15,   69,   70,   40,   72,   20,    3,
 /*   230 */    57,   76,   24,    7,    8,    7,    8,   10,   83,   66,
 /*   240 */    67,   56,   69,   70,   16,   72,    6,   11,    8,   76,
 /*   250 */    10,   43,   12,   77,   14,   79,   80,   20,   50,   86,
 /*   260 */    87,   24,   14,    3,   24,   25,   40,   16,   40,   18,
 /*   270 */    57,   35,   36,   12,   89,    3,   15,   37,   38,   66,
 /*   280 */    67,   21,   69,   70,    1,   72,   65,   50,   57,   76,
 /*   290 */    10,    3,   12,   21,   81,   15,   57,   66,   67,   78,
 /*   300 */    69,   70,   56,   72,   57,   66,   67,   76,   69,   70,
 /*   310 */    22,   72,   81,   66,   67,   76,   69,   70,   11,   72,
 /*   320 */    81,   66,   10,   76,   75,   70,   77,   72,   57,   17,
 /*   330 */    59,   76,   20,   50,   87,   89,   57,   66,   67,   64,
 /*   340 */    69,   70,   75,   72,   77,   66,   67,   76,   69,   70,
 /*   350 */    66,   72,   57,   78,   70,   76,   72,    7,    8,   66,
 /*   360 */    76,   66,   67,   57,   69,   70,   75,   72,   77,   76,
 /*   370 */    24,   76,   66,   67,   57,   69,   70,   64,   72,    7,
 /*   380 */     8,   88,   76,   66,   67,   13,   69,   70,   57,   72,
 /*   390 */    40,   78,   58,   76,   54,   55,   57,   66,   67,   24,
 /*   400 */    69,   70,   68,   72,   57,   66,   67,   76,   69,   70,
 /*   410 */    24,   72,   40,   66,   67,   76,   69,   70,   66,   72,
 /*   420 */    57,   24,   64,   76,   58,   71,   11,    3,   76,   66,
 /*   430 */    67,   57,   69,   70,   68,   72,   78,    3,   84,   76,
 /*   440 */    66,   67,   57,   69,   70,    1,   72,   12,   82,   14,
 /*   450 */    76,   66,   67,   64,   69,   70,   57,   72,   14,   42,
 /*   460 */    17,   76,   24,    3,    3,   66,   67,   78,   69,   70,
 /*   470 */    24,   72,   24,   24,   20,   76,   71,   89,   78,   14,
 /*   480 */    85,   37,   68,   77,   74,   74,   42,   24,   21,   90,
 /*   490 */    90,   90,   90,   90,   50,
);
    const YY_SHIFT_USE_DFLT = -27;
    const YY_SHIFT_MAX = 115;
    static public $yy_shift_ofst = array(
 /*     0 */    93,   75,   68,   68,   68,   68,  110,  240,  110,  110,
 /*    10 */   110,  110,  110,  110,  110,  110,  110,  110,  110,  110,
 /*    20 */   110,  110,  110,   26,   26,   26,  137,  141,  208,  164,
 /*    30 */   444,   86,   83,  184,   56,  435,   -7,   39,   93,   34,
 /*    40 */   -13,  -13,  280,  -13,  283,  261,  261,    6,  283,    6,
 /*    50 */   465,  467,  228,    9,  186,  372,  119,  226,  350,  102,
 /*    60 */   350,  350,  236,  350,  350,   96,  350,  169,  350,  237,
 /*    70 */    24,  261,  -26,  260,  -26,  272,  125,  261,  261,  -11,
 /*    80 */    54,  -17,  463,  -17,  149,  -17,  -17,  103,  130,  -27,
 /*    90 */   -27,  312,    2,  152,  251,  288,  205,  438,  454,  443,
 /*   100 */   417,  460,  448,  446,  424,  375,  307,  397,  248,  449,
 /*   110 */   227,  415,  386,  346,  434,  461,
);
    const YY_REDUCE_USE_DFLT = -48;
    const YY_REDUCE_MAX = 90;
    static public $yy_reduce_ofst = array(
 /*     0 */   -47,  -28,  155,  135,   88,  106,  173,  271,  247,  231,
 /*    10 */   213,  239,  317,  363,  331,  339,  347,  399,  374,  385,
 /*    20 */   306,  295,  279,  284,  255,   45,  122,  138,  138,   28,
 /*    30 */   293,  138,  132,  176,  366,  -14,  354,  354,  340,  221,
 /*    40 */   313,   55,  267,  275,  246,  291,  249,  358,  185,  389,
 /*    50 */   352,  334,  405,  405,  405,  405,  405,  405,  405,  388,
 /*    60 */   405,  405,  395,  405,  405,  395,  405,  388,  405,  400,
 /*    70 */   395,  406,  395,  414,  395,  414,  414,  406,  406,  410,
 /*    80 */   414,  -46,  411,  -46,  -37,  -46,  -46,   69,  118,    0,
 /*    90 */    47,
);
    static public $yyExpectedTokens = array(
        /* 0 */ array(1, 2, 4, 44, 46, 47, 48, 49, 50, ),
        /* 1 */ array(6, 8, 10, 12, 14, 24, 25, 34, 37, 38, ),
        /* 2 */ array(6, 8, 10, 12, 14, 24, 25, 34, 37, 38, ),
        /* 3 */ array(6, 8, 10, 12, 14, 24, 25, 34, 37, 38, ),
        /* 4 */ array(6, 8, 10, 12, 14, 24, 25, 34, 37, 38, ),
        /* 5 */ array(6, 8, 10, 12, 14, 24, 25, 34, 37, 38, ),
        /* 6 */ array(6, 8, 10, 12, 14, 24, 25, 37, 38, ),
        /* 7 */ array(6, 8, 10, 12, 14, 24, 25, 37, 38, ),
        /* 8 */ array(6, 8, 10, 12, 14, 24, 25, 37, 38, ),
        /* 9 */ array(6, 8, 10, 12, 14, 24, 25, 37, 38, ),
        /* 10 */ array(6, 8, 10, 12, 14, 24, 25, 37, 38, ),
        /* 11 */ array(6, 8, 10, 12, 14, 24, 25, 37, 38, ),
        /* 12 */ array(6, 8, 10, 12, 14, 24, 25, 37, 38, ),
        /* 13 */ array(6, 8, 10, 12, 14, 24, 25, 37, 38, ),
        /* 14 */ array(6, 8, 10, 12, 14, 24, 25, 37, 38, ),
        /* 15 */ array(6, 8, 10, 12, 14, 24, 25, 37, 38, ),
        /* 16 */ array(6, 8, 10, 12, 14, 24, 25, 37, 38, ),
        /* 17 */ array(6, 8, 10, 12, 14, 24, 25, 37, 38, ),
        /* 18 */ array(6, 8, 10, 12, 14, 24, 25, 37, 38, ),
        /* 19 */ array(6, 8, 10, 12, 14, 24, 25, 37, 38, ),
        /* 20 */ array(6, 8, 10, 12, 14, 24, 25, 37, 38, ),
        /* 21 */ array(6, 8, 10, 12, 14, 24, 25, 37, 38, ),
        /* 22 */ array(6, 8, 10, 12, 14, 24, 25, 37, 38, ),
        /* 23 */ array(6, 10, 14, 24, 25, 37, 38, ),
        /* 24 */ array(6, 10, 14, 24, 25, 37, 38, ),
        /* 25 */ array(6, 10, 14, 24, 25, 37, 38, ),
        /* 26 */ array(1, 14, 42, 50, ),
        /* 27 */ array(12, 15, 20, 24, 39, 43, 50, ),
        /* 28 */ array(12, 15, 20, 24, 43, 50, ),
        /* 29 */ array(3, 10, 17, 19, 21, 22, ),
        /* 30 */ array(1, 14, 37, 42, 50, ),
        /* 31 */ array(12, 15, 24, 43, 50, ),
        /* 32 */ array(7, 8, 21, 40, ),
        /* 33 */ array(12, 15, 22, ),
        /* 34 */ array(17, 21, ),
        /* 35 */ array(12, 14, ),
        /* 36 */ array(7, 8, 11, 26, 27, 28, 29, 30, 31, 32, 33, 40, ),
        /* 37 */ array(7, 8, 26, 27, 28, 29, 30, 31, 32, 33, 40, ),
        /* 38 */ array(1, 2, 4, 44, 46, 47, 48, 49, 50, ),
        /* 39 */ array(9, 20, 24, 50, ),
        /* 40 */ array(24, 41, 50, ),
        /* 41 */ array(24, 41, 50, ),
        /* 42 */ array(10, 12, 15, ),
        /* 43 */ array(24, 41, 50, ),
        /* 44 */ array(1, 50, ),
        /* 45 */ array(12, 15, ),
        /* 46 */ array(12, 15, ),
        /* 47 */ array(24, 50, ),
        /* 48 */ array(1, 50, ),
        /* 49 */ array(24, 50, ),
        /* 50 */ array(14, ),
        /* 51 */ array(21, ),
        /* 52 */ array(7, 8, 16, 40, ),
        /* 53 */ array(3, 7, 8, 40, ),
        /* 54 */ array(7, 8, 23, 40, ),
        /* 55 */ array(7, 8, 13, 40, ),
        /* 56 */ array(7, 8, 11, 40, ),
        /* 57 */ array(3, 7, 8, 40, ),
        /* 58 */ array(7, 8, 40, ),
        /* 59 */ array(1, 5, 50, ),
        /* 60 */ array(7, 8, 40, ),
        /* 61 */ array(7, 8, 40, ),
        /* 62 */ array(11, 35, 36, ),
        /* 63 */ array(7, 8, 40, ),
        /* 64 */ array(7, 8, 40, ),
        /* 65 */ array(18, 35, 36, ),
        /* 66 */ array(7, 8, 40, ),
        /* 67 */ array(1, 45, 50, ),
        /* 68 */ array(7, 8, 40, ),
        /* 69 */ array(20, 24, 50, ),
        /* 70 */ array(3, 35, 36, ),
        /* 71 */ array(12, 15, ),
        /* 72 */ array(35, 36, ),
        /* 73 */ array(3, 21, ),
        /* 74 */ array(35, 36, ),
        /* 75 */ array(3, 21, ),
        /* 76 */ array(3, 21, ),
        /* 77 */ array(12, 15, ),
        /* 78 */ array(12, 15, ),
        /* 79 */ array(14, 24, ),
        /* 80 */ array(3, 21, ),
        /* 81 */ array(19, ),
        /* 82 */ array(24, ),
        /* 83 */ array(19, ),
        /* 84 */ array(17, ),
        /* 85 */ array(19, ),
        /* 86 */ array(19, ),
        /* 87 */ array(22, ),
        /* 88 */ array(14, ),
        /* 89 */ array(),
        /* 90 */ array(),
        /* 91 */ array(10, 17, 20, ),
        /* 92 */ array(13, 16, ),
        /* 93 */ array(10, 17, ),
        /* 94 */ array(16, 18, ),
        /* 95 */ array(3, 22, ),
        /* 96 */ array(3, ),
        /* 97 */ array(24, ),
        /* 98 */ array(20, ),
        /* 99 */ array(17, ),
        /* 100 */ array(42, ),
        /* 101 */ array(3, ),
        /* 102 */ array(24, ),
        /* 103 */ array(24, ),
        /* 104 */ array(3, ),
        /* 105 */ array(24, ),
        /* 106 */ array(11, ),
        /* 107 */ array(24, ),
        /* 108 */ array(14, ),
        /* 109 */ array(24, ),
        /* 110 */ array(10, ),
        /* 111 */ array(11, ),
        /* 112 */ array(24, ),
        /* 113 */ array(24, ),
        /* 114 */ array(3, ),
        /* 115 */ array(3, ),
        /* 116 */ array(),
        /* 117 */ array(),
        /* 118 */ array(),
        /* 119 */ array(),
        /* 120 */ array(),
        /* 121 */ array(),
        /* 122 */ array(),
        /* 123 */ array(),
        /* 124 */ array(),
        /* 125 */ array(),
        /* 126 */ array(),
        /* 127 */ array(),
        /* 128 */ array(),
        /* 129 */ array(),
        /* 130 */ array(),
        /* 131 */ array(),
        /* 132 */ array(),
        /* 133 */ array(),
        /* 134 */ array(),
        /* 135 */ array(),
        /* 136 */ array(),
        /* 137 */ array(),
        /* 138 */ array(),
        /* 139 */ array(),
        /* 140 */ array(),
        /* 141 */ array(),
        /* 142 */ array(),
        /* 143 */ array(),
        /* 144 */ array(),
        /* 145 */ array(),
        /* 146 */ array(),
        /* 147 */ array(),
        /* 148 */ array(),
        /* 149 */ array(),
        /* 150 */ array(),
        /* 151 */ array(),
        /* 152 */ array(),
        /* 153 */ array(),
        /* 154 */ array(),
        /* 155 */ array(),
        /* 156 */ array(),
        /* 157 */ array(),
        /* 158 */ array(),
        /* 159 */ array(),
        /* 160 */ array(),
        /* 161 */ array(),
        /* 162 */ array(),
        /* 163 */ array(),
        /* 164 */ array(),
        /* 165 */ array(),
        /* 166 */ array(),
        /* 167 */ array(),
        /* 168 */ array(),
        /* 169 */ array(),
        /* 170 */ array(),
        /* 171 */ array(),
        /* 172 */ array(),
        /* 173 */ array(),
        /* 174 */ array(),
        /* 175 */ array(),
        /* 176 */ array(),
        /* 177 */ array(),
        /* 178 */ array(),
        /* 179 */ array(),
        /* 180 */ array(),
        /* 181 */ array(),
        /* 182 */ array(),
        /* 183 */ array(),
        /* 184 */ array(),
        /* 185 */ array(),
        /* 186 */ array(),
        /* 187 */ array(),
        /* 188 */ array(),
        /* 189 */ array(),
        /* 190 */ array(),
        /* 191 */ array(),
        /* 192 */ array(),
        /* 193 */ array(),
        /* 194 */ array(),
        /* 195 */ array(),
        /* 196 */ array(),
);
    static public $yy_default = array(
 /*     0 */   308,  308,  308,  308,  308,  308,  294,  308,  308,  270,
 /*    10 */   270,  270,  308,  308,  308,  308,  308,  308,  308,  308,
 /*    20 */   308,  308,  308,  308,  308,  308,  308,  254,  254,  245,
 /*    30 */   308,  254,  223,  248,  223,  308,  278,  278,  197,  308,
 /*    40 */   308,  308,  254,  308,  308,  254,  254,  308,  308,  308,
 /*    50 */   308,  223,  269,  308,  295,  308,  308,  308,  296,  308,
 /*    60 */   227,  274,  308,  279,  219,  308,  224,  308,  255,  308,
 /*    70 */   308,  244,  280,  308,  276,  308,  308,  250,  264,  308,
 /*    80 */   308,  233,  308,  231,  236,  232,  230,  261,  308,  273,
 /*    90 */   273,  245,  308,  245,  308,  308,  308,  308,  308,  308,
 /*   100 */   308,  308,  308,  308,  308,  308,  308,  308,  308,  308,
 /*   110 */   243,  308,  308,  308,  308,  308,  289,  277,  290,  213,
 /*   120 */   225,  218,  200,  288,  287,  286,  198,  199,  214,  275,
 /*   130 */   221,  208,  220,  307,  204,  305,  285,  203,  226,  205,
 /*   140 */   216,  201,  202,  207,  304,  306,  206,  217,  271,  249,
 /*   150 */   258,  262,  265,  235,  259,  234,  298,  257,  242,  260,
 /*   160 */   246,  263,  291,  267,  268,  292,  229,  251,  266,  247,
 /*   170 */   252,  256,  300,  301,  215,  293,  212,  237,  229,  228,
 /*   180 */   283,  282,  281,  272,  211,  238,  253,  299,  303,  302,
 /*   190 */   222,  209,  239,  210,  240,  241,  284,
);
/* The next thing included is series of defines which control
** various aspects of the generated parser.
**    self::YYNOCODE      is a number which corresponds
**                        to no legal terminal or nonterminal number.  This
**                        number is used to fill in empty slots of the hash 
**                        table.
**    self::YYFALLBACK    If defined, this indicates that one or more tokens
**                        have fall-back values which should be used if the
**                        original value of the token will not parse.
**    self::YYSTACKDEPTH  is the maximum depth of the parser's stack.
**    self::YYNSTATE      the combined number of states.
**    self::YYNRULE       the number of rules in the grammar
**    self::YYERRORSYMBOL is the code number of the error symbol.  If not
**                        defined, then do no error processing.
*/
    const YYNOCODE = 91;
    const YYSTACKDEPTH = 100;
    const YYNSTATE = 197;
    const YYNRULE = 111;
    const YYERRORSYMBOL = 51;
    const YYERRSYMDT = 'yy0';
    const YYFALLBACK = 1;
    /** The next table maps tokens into fallback tokens.  If a construct
     * like the following:
     * 
     *      %fallback ID X Y Z.
     *
     * appears in the grammer, then ID becomes a fallback token for X, Y,
     * and Z.  Whenever one of the tokens X, Y, or Z is input to the parser
     * but it does not parse, the type of the token is changed to ID and
     * the parse is retried before an error is thrown.
     */
    static public $yyFallback = array(
    0,  /*          $ => nothing */
    0,  /*      OTHER => nothing */
    1,  /*  LDELSLASH => OTHER */
    1,  /*       RDEL => OTHER */
    1,  /* COMMENTSTART => OTHER */
    1,  /* COMMENTEND => OTHER */
    1,  /*     NUMBER => OTHER */
    1,  /*       MATH => OTHER */
    1,  /*    UNIMATH => OTHER */
    1,  /*     INCDEC => OTHER */
    1,  /*      OPENP => OTHER */
    1,  /*     CLOSEP => OTHER */
    1,  /*      OPENB => OTHER */
    1,  /*     CLOSEB => OTHER */
    1,  /*     DOLLAR => OTHER */
    1,  /*        DOT => OTHER */
    1,  /*      COMMA => OTHER */
    1,  /*      COLON => OTHER */
    1,  /*  SEMICOLON => OTHER */
    1,  /*       VERT => OTHER */
    1,  /*      EQUAL => OTHER */
    1,  /*      SPACE => OTHER */
    1,  /*        PTR => OTHER */
    1,  /*       APTR => OTHER */
    1,  /*         ID => OTHER */
    1,  /*    SI_QSTR => OTHER */
    1,  /*     EQUALS => OTHER */
    1,  /*  NOTEQUALS => OTHER */
    1,  /* GREATERTHAN => OTHER */
    1,  /*   LESSTHAN => OTHER */
    1,  /* GREATEREQUAL => OTHER */
    1,  /*  LESSEQUAL => OTHER */
    1,  /*   IDENTITY => OTHER */
    1,  /* NONEIDENTITY => OTHER */
    1,  /*        NOT => OTHER */
    1,  /*       LAND => OTHER */
    1,  /*        LOR => OTHER */
    1,  /*      QUOTE => OTHER */
    1,  /*    BOOLEAN => OTHER */
    1,  /*         IN => OTHER */
    1,  /*     ANDSYM => OTHER */
    1,  /*     UNDERL => OTHER */
    1,  /*   BACKTICK => OTHER */
    1,  /*         AT => OTHER */
    0,  /* LITERALSTART => nothing */
    0,  /* LITERALEND => nothing */
    0,  /*  LDELIMTAG => nothing */
    0,  /*  RDELIMTAG => nothing */
    0,  /*        PHP => nothing */
    0,  /*        XML => nothing */
    0,  /*       LDEL => nothing */
    );
    /**
     * Turn parser tracing on by giving a stream to which to write the trace
     * and a prompt to preface each trace message.  Tracing is turned off
     * by making either argument NULL 
     *
     * Inputs:
     * 
     * - A stream resource to which trace output should be written.
     *   If NULL, then tracing is turned off.
     * - A prefix string written at the beginning of every
     *   line of trace output.  If NULL, then tracing is
     *   turned off.
     *
     * Outputs:
     * 
     * - None.
     * @param resource
     * @param string
     */
    static function Trace($TraceFILE, $zTracePrompt)
    {
        if (!$TraceFILE) {
            $zTracePrompt = 0;
        } elseif (!$zTracePrompt) {
            $TraceFILE = 0;
        }
        self::$yyTraceFILE = $TraceFILE;
        self::$yyTracePrompt = $zTracePrompt;
    }

    /**
     * Output debug information to output (php://output stream)
     */
    static function PrintTrace()
    {
        self::$yyTraceFILE = fopen('php://output', 'w');
        self::$yyTracePrompt = '<br>';
    }

    /**
     * @var resource|0
     */
    static public $yyTraceFILE;
    /**
     * String to prepend to debug output
     * @var string|0
     */
    static public $yyTracePrompt;
    /**
     * @var int
     */
    public $yyidx;                    /* Index of top element in stack */
    /**
     * @var int
     */
    public $yyerrcnt;                 /* Shifts left before out of the error */
    /**
     * @var array
     */
    public $yystack = array();  /* The parser's stack */

    /**
     * For tracing shifts, the names of all terminals and nonterminals
     * are required.  The following table supplies these names
     * @var array
     */
    public $yyTokenName = array( 
  '$',             'OTHER',         'LDELSLASH',     'RDEL',        
  'COMMENTSTART',  'COMMENTEND',    'NUMBER',        'MATH',        
  'UNIMATH',       'INCDEC',        'OPENP',         'CLOSEP',      
  'OPENB',         'CLOSEB',        'DOLLAR',        'DOT',         
  'COMMA',         'COLON',         'SEMICOLON',     'VERT',        
  'EQUAL',         'SPACE',         'PTR',           'APTR',        
  'ID',            'SI_QSTR',       'EQUALS',        'NOTEQUALS',   
  'GREATERTHAN',   'LESSTHAN',      'GREATEREQUAL',  'LESSEQUAL',   
  'IDENTITY',      'NONEIDENTITY',  'NOT',           'LAND',        
  'LOR',           'QUOTE',         'BOOLEAN',       'IN',          
  'ANDSYM',        'UNDERL',        'BACKTICK',      'AT',          
  'LITERALSTART',  'LITERALEND',    'LDELIMTAG',     'RDELIMTAG',   
  'PHP',           'XML',           'LDEL',          'error',       
  'start',         'template',      'template_element',  'smartytag',   
  'text',          'expr',          'attributes',    'statement',   
  'modifier',      'modparameters',  'ifexprs',       'statements',  
  'varvar',        'foraction',     'variable',      'array',       
  'attribute',     'exprs',         'value',         'math',        
  'function',      'doublequoted',  'method',        'vararraydefs',
  'object',        'vararraydef',   'varvarele',     'objectchain', 
  'objectelement',  'params',        'modparameter',  'ifexpr',      
  'ifcond',        'lop',           'arrayelements',  'arrayelement',
  'doublequotedcontent',  'textelement', 
    );

    /**
     * For tracing reduce actions, the names of all rules are required.
     * @var array
     */
    static public $yyRuleName = array(
 /*   0 */ "start ::= template",
 /*   1 */ "template ::= template_element",
 /*   2 */ "template ::= template template_element",
 /*   3 */ "template_element ::= smartytag",
 /*   4 */ "template_element ::= COMMENTSTART text COMMENTEND",
 /*   5 */ "template_element ::= LITERALSTART text LITERALEND",
 /*   6 */ "template_element ::= LDELIMTAG",
 /*   7 */ "template_element ::= RDELIMTAG",
 /*   8 */ "template_element ::= PHP",
 /*   9 */ "template_element ::= XML",
 /*  10 */ "template_element ::= OTHER",
 /*  11 */ "smartytag ::= LDEL expr attributes RDEL",
 /*  12 */ "smartytag ::= LDEL statement RDEL",
 /*  13 */ "smartytag ::= LDEL ID attributes RDEL",
 /*  14 */ "smartytag ::= LDEL ID PTR ID attributes RDEL",
 /*  15 */ "smartytag ::= LDEL ID modifier modparameters attributes RDEL",
 /*  16 */ "smartytag ::= LDELSLASH ID RDEL",
 /*  17 */ "smartytag ::= LDELSLASH ID PTR ID RDEL",
 /*  18 */ "smartytag ::= LDEL ID SPACE ifexprs RDEL",
 /*  19 */ "smartytag ::= LDEL ID SPACE statements SEMICOLON ifexprs SEMICOLON DOLLAR varvar foraction RDEL",
 /*  20 */ "smartytag ::= LDEL ID SPACE DOLLAR varvar IN variable RDEL",
 /*  21 */ "smartytag ::= LDEL ID SPACE DOLLAR varvar IN array RDEL",
 /*  22 */ "foraction ::= EQUAL expr",
 /*  23 */ "foraction ::= INCDEC",
 /*  24 */ "attributes ::= attributes attribute",
 /*  25 */ "attributes ::= attribute",
 /*  26 */ "attributes ::=",
 /*  27 */ "attribute ::= SPACE ID EQUAL expr",
 /*  28 */ "statements ::= statement",
 /*  29 */ "statements ::= statements COMMA statement",
 /*  30 */ "statement ::= DOLLAR varvar EQUAL expr",
 /*  31 */ "expr ::= exprs",
 /*  32 */ "expr ::= array",
 /*  33 */ "exprs ::= value",
 /*  34 */ "exprs ::= UNIMATH value",
 /*  35 */ "exprs ::= expr math value",
 /*  36 */ "exprs ::= expr ANDSYM value",
 /*  37 */ "math ::= UNIMATH",
 /*  38 */ "math ::= MATH",
 /*  39 */ "value ::= value modifier modparameters",
 /*  40 */ "value ::= variable",
 /*  41 */ "value ::= NUMBER",
 /*  42 */ "value ::= function",
 /*  43 */ "value ::= SI_QSTR",
 /*  44 */ "value ::= QUOTE doublequoted QUOTE",
 /*  45 */ "value ::= ID COLON COLON method",
 /*  46 */ "value ::= ID COLON COLON ID",
 /*  47 */ "value ::= ID COLON COLON DOLLAR ID vararraydefs",
 /*  48 */ "value ::= ID",
 /*  49 */ "value ::= BOOLEAN",
 /*  50 */ "value ::= OPENP expr CLOSEP",
 /*  51 */ "variable ::= DOLLAR varvar vararraydefs",
 /*  52 */ "variable ::= DOLLAR varvar AT ID",
 /*  53 */ "variable ::= DOLLAR UNDERL ID vararraydefs",
 /*  54 */ "variable ::= object",
 /*  55 */ "vararraydefs ::= vararraydef",
 /*  56 */ "vararraydefs ::= vararraydefs vararraydef",
 /*  57 */ "vararraydefs ::=",
 /*  58 */ "vararraydef ::= DOT expr",
 /*  59 */ "vararraydef ::= OPENB expr CLOSEB",
 /*  60 */ "varvar ::= varvarele",
 /*  61 */ "varvar ::= varvar varvarele",
 /*  62 */ "varvarele ::= ID",
 /*  63 */ "varvarele ::= LDEL expr RDEL",
 /*  64 */ "object ::= DOLLAR varvar vararraydefs objectchain",
 /*  65 */ "objectchain ::= objectelement",
 /*  66 */ "objectchain ::= objectchain objectelement",
 /*  67 */ "objectelement ::= PTR ID vararraydefs",
 /*  68 */ "objectelement ::= PTR method",
 /*  69 */ "function ::= ID OPENP params CLOSEP",
 /*  70 */ "method ::= ID OPENP params CLOSEP",
 /*  71 */ "params ::= expr COMMA params",
 /*  72 */ "params ::= expr",
 /*  73 */ "params ::=",
 /*  74 */ "modifier ::= VERT ID",
 /*  75 */ "modparameters ::= modparameters modparameter",
 /*  76 */ "modparameters ::=",
 /*  77 */ "modparameter ::= COLON expr",
 /*  78 */ "ifexprs ::= ifexpr",
 /*  79 */ "ifexprs ::= NOT ifexprs",
 /*  80 */ "ifexprs ::= OPENP ifexprs CLOSEP",
 /*  81 */ "ifexpr ::= expr",
 /*  82 */ "ifexpr ::= expr ifcond expr",
 /*  83 */ "ifexpr ::= ifexprs lop ifexprs",
 /*  84 */ "ifcond ::= EQUALS",
 /*  85 */ "ifcond ::= NOTEQUALS",
 /*  86 */ "ifcond ::= GREATERTHAN",
 /*  87 */ "ifcond ::= LESSTHAN",
 /*  88 */ "ifcond ::= GREATEREQUAL",
 /*  89 */ "ifcond ::= LESSEQUAL",
 /*  90 */ "ifcond ::= IDENTITY",
 /*  91 */ "ifcond ::= NONEIDENTITY",
 /*  92 */ "lop ::= LAND",
 /*  93 */ "lop ::= LOR",
 /*  94 */ "array ::= OPENB arrayelements CLOSEB",
 /*  95 */ "arrayelements ::= arrayelement",
 /*  96 */ "arrayelements ::= arrayelements COMMA arrayelement",
 /*  97 */ "arrayelements ::=",
 /*  98 */ "arrayelement ::= expr",
 /*  99 */ "arrayelement ::= expr APTR expr",
 /* 100 */ "arrayelement ::= array",
 /* 101 */ "doublequoted ::= doublequoted doublequotedcontent",
 /* 102 */ "doublequoted ::= doublequotedcontent",
 /* 103 */ "doublequotedcontent ::= variable",
 /* 104 */ "doublequotedcontent ::= BACKTICK variable BACKTICK",
 /* 105 */ "doublequotedcontent ::= LDEL expr RDEL",
 /* 106 */ "doublequotedcontent ::= OTHER",
 /* 107 */ "text ::= text textelement",
 /* 108 */ "text ::= textelement",
 /* 109 */ "textelement ::= OTHER",
 /* 110 */ "textelement ::= LDEL",
    );

    /**
     * This function returns the symbolic name associated with a token
     * value.
     * @param int
     * @return string
     */
    function tokenName($tokenType)
    {
        if ($tokenType === 0) {
            return 'End of Input';
        }
        if ($tokenType > 0 && $tokenType < count($this->yyTokenName)) {
            return $this->yyTokenName[$tokenType];
        } else {
            return "Unknown";
        }
    }

    /**
     * The following function deletes the value associated with a
     * symbol.  The symbol can be either a terminal or nonterminal.
     * @param int the symbol code
     * @param mixed the symbol's value
     */
    static function yy_destructor($yymajor, $yypminor)
    {
        switch ($yymajor) {
        /* Here is inserted the actions which take place when a
        ** terminal or non-terminal is destroyed.  This can happen
        ** when the symbol is popped from the stack during a
        ** reduce or during error processing or when a parser is 
        ** being destroyed before it is finished parsing.
        **
        ** Note: during a reduce, the only symbols destroyed are those
        ** which appear on the RHS of the rule, but which are not used
        ** inside the C code.
        */
            default:  break;   /* If no destructor action specified: do nothing */
        }
    }

    /**
     * Pop the parser's stack once.
     *
     * If there is a destructor routine associated with the token which
     * is popped from the stack, then call it.
     *
     * Return the major token number for the symbol popped.
     * @param TP_yyParser
     * @return int
     */
    function yy_pop_parser_stack()
    {
        if (!count($this->yystack)) {
            return;
        }
        $yytos = array_pop($this->yystack);
        if (self::$yyTraceFILE && $this->yyidx >= 0) {
            fwrite(self::$yyTraceFILE,
                self::$yyTracePrompt . 'Popping ' . $this->yyTokenName[$yytos->major] .
                    "\n");
        }
        $yymajor = $yytos->major;
        self::yy_destructor($yymajor, $yytos->minor);
        $this->yyidx--;
        return $yymajor;
    }

    /**
     * Deallocate and destroy a parser.  Destructors are all called for
     * all stack elements before shutting the parser down.
     */
    function __destruct()
    {
        while ($this->yyidx >= 0) {
            $this->yy_pop_parser_stack();
        }
        if (is_resource(self::$yyTraceFILE)) {
            fclose(self::$yyTraceFILE);
        }
    }

    /**
     * Based on the current state and parser stack, get a list of all
     * possible lookahead tokens
     * @param int
     * @return array
     */
    function yy_get_expected_tokens($token)
    {
        $state = $this->yystack[$this->yyidx]->stateno;
        $expected = self::$yyExpectedTokens[$state];
        if (in_array($token, self::$yyExpectedTokens[$state], true)) {
            return $expected;
        }
        $stack = $this->yystack;
        $yyidx = $this->yyidx;
        do {
            $yyact = $this->yy_find_shift_action($token);
            if ($yyact >= self::YYNSTATE && $yyact < self::YYNSTATE + self::YYNRULE) {
                // reduce action
                $done = 0;
                do {
                    if ($done++ == 100) {
                        $this->yyidx = $yyidx;
                        $this->yystack = $stack;
                        // too much recursion prevents proper detection
                        // so give up
                        return array_unique($expected);
                    }
                    $yyruleno = $yyact - self::YYNSTATE;
                    $this->yyidx -= self::$yyRuleInfo[$yyruleno]['rhs'];
                    $nextstate = $this->yy_find_reduce_action(
                        $this->yystack[$this->yyidx]->stateno,
                        self::$yyRuleInfo[$yyruleno]['lhs']);
                    if (isset(self::$yyExpectedTokens[$nextstate])) {
                        $expected += self::$yyExpectedTokens[$nextstate];
                            if (in_array($token,
                                  self::$yyExpectedTokens[$nextstate], true)) {
                            $this->yyidx = $yyidx;
                            $this->yystack = $stack;
                            return array_unique($expected);
                        }
                    }
                    if ($nextstate < self::YYNSTATE) {
                        // we need to shift a non-terminal
                        $this->yyidx++;
                        $x = new TP_yyStackEntry;
                        $x->stateno = $nextstate;
                        $x->major = self::$yyRuleInfo[$yyruleno]['lhs'];
                        $this->yystack[$this->yyidx] = $x;
                        continue 2;
                    } elseif ($nextstate == self::YYNSTATE + self::YYNRULE + 1) {
                        $this->yyidx = $yyidx;
                        $this->yystack = $stack;
                        // the last token was just ignored, we can't accept
                        // by ignoring input, this is in essence ignoring a
                        // syntax error!
                        return array_unique($expected);
                    } elseif ($nextstate === self::YY_NO_ACTION) {
                        $this->yyidx = $yyidx;
                        $this->yystack = $stack;
                        // input accepted, but not shifted (I guess)
                        return $expected;
                    } else {
                        $yyact = $nextstate;
                    }
                } while (true);
            }
            break;
        } while (true);
        return array_unique($expected);
    }

    /**
     * Based on the parser state and current parser stack, determine whether
     * the lookahead token is possible.
     * 
     * The parser will convert the token value to an error token if not.  This
     * catches some unusual edge cases where the parser would fail.
     * @param int
     * @return bool
     */
    function yy_is_expected_token($token)
    {
        if ($token === 0) {
            return true; // 0 is not part of this
        }
        $state = $this->yystack[$this->yyidx]->stateno;
        if (in_array($token, self::$yyExpectedTokens[$state], true)) {
            return true;
        }
        $stack = $this->yystack;
        $yyidx = $this->yyidx;
        do {
            $yyact = $this->yy_find_shift_action($token);
            if ($yyact >= self::YYNSTATE && $yyact < self::YYNSTATE + self::YYNRULE) {
                // reduce action
                $done = 0;
                do {
                    if ($done++ == 100) {
                        $this->yyidx = $yyidx;
                        $this->yystack = $stack;
                        // too much recursion prevents proper detection
                        // so give up
                        return true;
                    }
                    $yyruleno = $yyact - self::YYNSTATE;
                    $this->yyidx -= self::$yyRuleInfo[$yyruleno]['rhs'];
                    $nextstate = $this->yy_find_reduce_action(
                        $this->yystack[$this->yyidx]->stateno,
                        self::$yyRuleInfo[$yyruleno]['lhs']);
                    if (isset(self::$yyExpectedTokens[$nextstate]) &&
                          in_array($token, self::$yyExpectedTokens[$nextstate], true)) {
                        $this->yyidx = $yyidx;
                        $this->yystack = $stack;
                        return true;
                    }
                    if ($nextstate < self::YYNSTATE) {
                        // we need to shift a non-terminal
                        $this->yyidx++;
                        $x = new TP_yyStackEntry;
                        $x->stateno = $nextstate;
                        $x->major = self::$yyRuleInfo[$yyruleno]['lhs'];
                        $this->yystack[$this->yyidx] = $x;
                        continue 2;
                    } elseif ($nextstate == self::YYNSTATE + self::YYNRULE + 1) {
                        $this->yyidx = $yyidx;
                        $this->yystack = $stack;
                        if (!$token) {
                            // end of input: this is valid
                            return true;
                        }
                        // the last token was just ignored, we can't accept
                        // by ignoring input, this is in essence ignoring a
                        // syntax error!
                        return false;
                    } elseif ($nextstate === self::YY_NO_ACTION) {
                        $this->yyidx = $yyidx;
                        $this->yystack = $stack;
                        // input accepted, but not shifted (I guess)
                        return true;
                    } else {
                        $yyact = $nextstate;
                    }
                } while (true);
            }
            break;
        } while (true);
        $this->yyidx = $yyidx;
        $this->yystack = $stack;
        return true;
    }

    /**
     * Find the appropriate action for a parser given the terminal
     * look-ahead token iLookAhead.
     *
     * If the look-ahead token is YYNOCODE, then check to see if the action is
     * independent of the look-ahead.  If it is, return the action, otherwise
     * return YY_NO_ACTION.
     * @param int The look-ahead token
     */
    function yy_find_shift_action($iLookAhead)
    {
        $stateno = $this->yystack[$this->yyidx]->stateno;
     
        /* if ($this->yyidx < 0) return self::YY_NO_ACTION;  */
        if (!isset(self::$yy_shift_ofst[$stateno])) {
            // no shift actions
            return self::$yy_default[$stateno];
        }
        $i = self::$yy_shift_ofst[$stateno];
        if ($i === self::YY_SHIFT_USE_DFLT) {
            return self::$yy_default[$stateno];
        }
        if ($iLookAhead == self::YYNOCODE) {
            return self::YY_NO_ACTION;
        }
        $i += $iLookAhead;
        if ($i < 0 || $i >= self::YY_SZ_ACTTAB ||
              self::$yy_lookahead[$i] != $iLookAhead) {
            if (count(self::$yyFallback) && $iLookAhead < count(self::$yyFallback)
                   && ($iFallback = self::$yyFallback[$iLookAhead]) != 0) {
                if (self::$yyTraceFILE) {
                    fwrite(self::$yyTraceFILE, self::$yyTracePrompt . "FALLBACK " .
                        $this->yyTokenName[$iLookAhead] . " => " .
                        $this->yyTokenName[$iFallback] . "\n");
                }
                return $this->yy_find_shift_action($iFallback);
            }
            return self::$yy_default[$stateno];
        } else {
            return self::$yy_action[$i];
        }
    }

    /**
     * Find the appropriate action for a parser given the non-terminal
     * look-ahead token $iLookAhead.
     *
     * If the look-ahead token is self::YYNOCODE, then check to see if the action is
     * independent of the look-ahead.  If it is, return the action, otherwise
     * return self::YY_NO_ACTION.
     * @param int Current state number
     * @param int The look-ahead token
     */
    function yy_find_reduce_action($stateno, $iLookAhead)
    {
        /* $stateno = $this->yystack[$this->yyidx]->stateno; */

        if (!isset(self::$yy_reduce_ofst[$stateno])) {
            return self::$yy_default[$stateno];
        }
        $i = self::$yy_reduce_ofst[$stateno];
        if ($i == self::YY_REDUCE_USE_DFLT) {
            return self::$yy_default[$stateno];
        }
        if ($iLookAhead == self::YYNOCODE) {
            return self::YY_NO_ACTION;
        }
        $i += $iLookAhead;
        if ($i < 0 || $i >= self::YY_SZ_ACTTAB ||
              self::$yy_lookahead[$i] != $iLookAhead) {
            return self::$yy_default[$stateno];
        } else {
            return self::$yy_action[$i];
        }
    }

    /**
     * Perform a shift action.
     * @param int The new state to shift in
     * @param int The major token to shift in
     * @param mixed the minor token to shift in
     */
    function yy_shift($yyNewState, $yyMajor, $yypMinor)
    {
        $this->yyidx++;
        if ($this->yyidx >= self::YYSTACKDEPTH) {
            $this->yyidx--;
            if (self::$yyTraceFILE) {
                fprintf(self::$yyTraceFILE, "%sStack Overflow!\n", self::$yyTracePrompt);
            }
            while ($this->yyidx >= 0) {
                $this->yy_pop_parser_stack();
            }
            /* Here code is inserted which will execute if the parser
            ** stack ever overflows */
            return;
        }
        $yytos = new TP_yyStackEntry;
        $yytos->stateno = $yyNewState;
        $yytos->major = $yyMajor;
        $yytos->minor = $yypMinor;
        array_push($this->yystack, $yytos);
        if (self::$yyTraceFILE && $this->yyidx > 0) {
            fprintf(self::$yyTraceFILE, "%sShift %d\n", self::$yyTracePrompt,
                $yyNewState);
            fprintf(self::$yyTraceFILE, "%sStack:", self::$yyTracePrompt);
            for($i = 1; $i <= $this->yyidx; $i++) {
                fprintf(self::$yyTraceFILE, " %s",
                    $this->yyTokenName[$this->yystack[$i]->major]);
            }
            fwrite(self::$yyTraceFILE,"\n");
        }
    }

    /**
     * The following table contains information about every rule that
     * is used during the reduce.
     *
     * <pre>
     * array(
     *  array(
     *   int $lhs;         Symbol on the left-hand side of the rule
     *   int $nrhs;     Number of right-hand side symbols in the rule
     *  ),...
     * );
     * </pre>
     */
    static public $yyRuleInfo = array(
  array( 'lhs' => 52, 'rhs' => 1 ),
  array( 'lhs' => 53, 'rhs' => 1 ),
  array( 'lhs' => 53, 'rhs' => 2 ),
  array( 'lhs' => 54, 'rhs' => 1 ),
  array( 'lhs' => 54, 'rhs' => 3 ),
  array( 'lhs' => 54, 'rhs' => 3 ),
  array( 'lhs' => 54, 'rhs' => 1 ),
  array( 'lhs' => 54, 'rhs' => 1 ),
  array( 'lhs' => 54, 'rhs' => 1 ),
  array( 'lhs' => 54, 'rhs' => 1 ),
  array( 'lhs' => 54, 'rhs' => 1 ),
  array( 'lhs' => 55, 'rhs' => 4 ),
  array( 'lhs' => 55, 'rhs' => 3 ),
  array( 'lhs' => 55, 'rhs' => 4 ),
  array( 'lhs' => 55, 'rhs' => 6 ),
  array( 'lhs' => 55, 'rhs' => 6 ),
  array( 'lhs' => 55, 'rhs' => 3 ),
  array( 'lhs' => 55, 'rhs' => 5 ),
  array( 'lhs' => 55, 'rhs' => 5 ),
  array( 'lhs' => 55, 'rhs' => 11 ),
  array( 'lhs' => 55, 'rhs' => 8 ),
  array( 'lhs' => 55, 'rhs' => 8 ),
  array( 'lhs' => 65, 'rhs' => 2 ),
  array( 'lhs' => 65, 'rhs' => 1 ),
  array( 'lhs' => 58, 'rhs' => 2 ),
  array( 'lhs' => 58, 'rhs' => 1 ),
  array( 'lhs' => 58, 'rhs' => 0 ),
  array( 'lhs' => 68, 'rhs' => 4 ),
  array( 'lhs' => 63, 'rhs' => 1 ),
  array( 'lhs' => 63, 'rhs' => 3 ),
  array( 'lhs' => 59, 'rhs' => 4 ),
  array( 'lhs' => 57, 'rhs' => 1 ),
  array( 'lhs' => 57, 'rhs' => 1 ),
  array( 'lhs' => 69, 'rhs' => 1 ),
  array( 'lhs' => 69, 'rhs' => 2 ),
  array( 'lhs' => 69, 'rhs' => 3 ),
  array( 'lhs' => 69, 'rhs' => 3 ),
  array( 'lhs' => 71, 'rhs' => 1 ),
  array( 'lhs' => 71, 'rhs' => 1 ),
  array( 'lhs' => 70, 'rhs' => 3 ),
  array( 'lhs' => 70, 'rhs' => 1 ),
  array( 'lhs' => 70, 'rhs' => 1 ),
  array( 'lhs' => 70, 'rhs' => 1 ),
  array( 'lhs' => 70, 'rhs' => 1 ),
  array( 'lhs' => 70, 'rhs' => 3 ),
  array( 'lhs' => 70, 'rhs' => 4 ),
  array( 'lhs' => 70, 'rhs' => 4 ),
  array( 'lhs' => 70, 'rhs' => 6 ),
  array( 'lhs' => 70, 'rhs' => 1 ),
  array( 'lhs' => 70, 'rhs' => 1 ),
  array( 'lhs' => 70, 'rhs' => 3 ),
  array( 'lhs' => 66, 'rhs' => 3 ),
  array( 'lhs' => 66, 'rhs' => 4 ),
  array( 'lhs' => 66, 'rhs' => 4 ),
  array( 'lhs' => 66, 'rhs' => 1 ),
  array( 'lhs' => 75, 'rhs' => 1 ),
  array( 'lhs' => 75, 'rhs' => 2 ),
  array( 'lhs' => 75, 'rhs' => 0 ),
  array( 'lhs' => 77, 'rhs' => 2 ),
  array( 'lhs' => 77, 'rhs' => 3 ),
  array( 'lhs' => 64, 'rhs' => 1 ),
  array( 'lhs' => 64, 'rhs' => 2 ),
  array( 'lhs' => 78, 'rhs' => 1 ),
  array( 'lhs' => 78, 'rhs' => 3 ),
  array( 'lhs' => 76, 'rhs' => 4 ),
  array( 'lhs' => 79, 'rhs' => 1 ),
  array( 'lhs' => 79, 'rhs' => 2 ),
  array( 'lhs' => 80, 'rhs' => 3 ),
  array( 'lhs' => 80, 'rhs' => 2 ),
  array( 'lhs' => 72, 'rhs' => 4 ),
  array( 'lhs' => 74, 'rhs' => 4 ),
  array( 'lhs' => 81, 'rhs' => 3 ),
  array( 'lhs' => 81, 'rhs' => 1 ),
  array( 'lhs' => 81, 'rhs' => 0 ),
  array( 'lhs' => 60, 'rhs' => 2 ),
  array( 'lhs' => 61, 'rhs' => 2 ),
  array( 'lhs' => 61, 'rhs' => 0 ),
  array( 'lhs' => 82, 'rhs' => 2 ),
  array( 'lhs' => 62, 'rhs' => 1 ),
  array( 'lhs' => 62, 'rhs' => 2 ),
  array( 'lhs' => 62, 'rhs' => 3 ),
  array( 'lhs' => 83, 'rhs' => 1 ),
  array( 'lhs' => 83, 'rhs' => 3 ),
  array( 'lhs' => 83, 'rhs' => 3 ),
  array( 'lhs' => 84, 'rhs' => 1 ),
  array( 'lhs' => 84, 'rhs' => 1 ),
  array( 'lhs' => 84, 'rhs' => 1 ),
  array( 'lhs' => 84, 'rhs' => 1 ),
  array( 'lhs' => 84, 'rhs' => 1 ),
  array( 'lhs' => 84, 'rhs' => 1 ),
  array( 'lhs' => 84, 'rhs' => 1 ),
  array( 'lhs' => 84, 'rhs' => 1 ),
  array( 'lhs' => 85, 'rhs' => 1 ),
  array( 'lhs' => 85, 'rhs' => 1 ),
  array( 'lhs' => 67, 'rhs' => 3 ),
  array( 'lhs' => 86, 'rhs' => 1 ),
  array( 'lhs' => 86, 'rhs' => 3 ),
  array( 'lhs' => 86, 'rhs' => 0 ),
  array( 'lhs' => 87, 'rhs' => 1 ),
  array( 'lhs' => 87, 'rhs' => 3 ),
  array( 'lhs' => 87, 'rhs' => 1 ),
  array( 'lhs' => 73, 'rhs' => 2 ),
  array( 'lhs' => 73, 'rhs' => 1 ),
  array( 'lhs' => 88, 'rhs' => 1 ),
  array( 'lhs' => 88, 'rhs' => 3 ),
  array( 'lhs' => 88, 'rhs' => 3 ),
  array( 'lhs' => 88, 'rhs' => 1 ),
  array( 'lhs' => 56, 'rhs' => 2 ),
  array( 'lhs' => 56, 'rhs' => 1 ),
  array( 'lhs' => 89, 'rhs' => 1 ),
  array( 'lhs' => 89, 'rhs' => 1 ),
    );

    /**
     * The following table contains a mapping of reduce action to method name
     * that handles the reduction.
     * 
     * If a rule is not set, it has no handler.
     */
    static public $yyReduceMap = array(
        0 => 0,
        33 => 0,
        40 => 0,
        41 => 0,
        42 => 0,
        43 => 0,
        49 => 0,
        54 => 0,
        95 => 0,
        1 => 1,
        31 => 1,
        32 => 1,
        37 => 1,
        38 => 1,
        55 => 1,
        60 => 1,
        78 => 1,
        102 => 1,
        108 => 1,
        109 => 1,
        110 => 1,
        2 => 2,
        56 => 2,
        101 => 2,
        107 => 2,
        3 => 3,
        4 => 4,
        5 => 5,
        6 => 6,
        7 => 7,
        8 => 8,
        9 => 9,
        10 => 10,
        11 => 11,
        12 => 12,
        13 => 13,
        14 => 14,
        15 => 15,
        16 => 16,
        17 => 17,
        18 => 18,
        19 => 19,
        20 => 20,
        21 => 20,
        22 => 22,
        23 => 23,
        25 => 23,
        72 => 23,
        98 => 23,
        100 => 23,
        24 => 24,
        26 => 26,
        27 => 27,
        28 => 28,
        29 => 29,
        30 => 30,
        34 => 34,
        35 => 35,
        36 => 36,
        39 => 39,
        44 => 44,
        45 => 45,
        46 => 46,
        47 => 47,
        48 => 48,
        50 => 50,
        51 => 51,
        52 => 52,
        53 => 53,
        57 => 57,
        76 => 57,
        58 => 58,
        59 => 59,
        61 => 61,
        62 => 62,
        63 => 63,
        80 => 63,
        64 => 64,
        65 => 65,
        66 => 66,
        67 => 67,
        68 => 68,
        69 => 69,
        70 => 70,
        71 => 71,
        73 => 73,
        74 => 74,
        75 => 75,
        77 => 77,
        79 => 79,
        81 => 81,
        82 => 82,
        83 => 82,
        84 => 84,
        85 => 85,
        86 => 86,
        87 => 87,
        88 => 88,
        89 => 89,
        90 => 90,
        91 => 91,
        92 => 92,
        93 => 93,
        94 => 94,
        96 => 96,
        97 => 97,
        99 => 99,
        103 => 103,
        104 => 104,
        105 => 105,
        106 => 106,
    );
    /* Beginning here are the reduction cases.  A typical example
    ** follows:
    **  #line <lineno> <grammarfile>
    **   function yy_r0($yymsp){ ... }           // User supplied code
    **  #line <lineno> <thisfile>
    */
#line 69 "internal.templateparser.y"
    function yy_r0(){ $this->_retvalue = $this->yystack[$this->yyidx + 0]->minor;     }
#line 1510 "internal.templateparser.php"
#line 75 "internal.templateparser.y"
    function yy_r1(){$this->_retvalue = $this->yystack[$this->yyidx + 0]->minor;    }
#line 1513 "internal.templateparser.php"
#line 77 "internal.templateparser.y"
    function yy_r2(){$this->_retvalue = $this->yystack[$this->yyidx + -1]->minor.$this->yystack[$this->yyidx + 0]->minor;    }
#line 1516 "internal.templateparser.php"
#line 83 "internal.templateparser.y"
    function yy_r3(){if ($this->compiler->has_code) {
                                            $this->_retvalue = $this->cacher->processNocacheCode($this->yystack[$this->yyidx + 0]->minor, $this->compiler,$this->nocache,true);
                                         } $this->nocache=false;    }
#line 1521 "internal.templateparser.php"
#line 87 "internal.templateparser.y"
    function yy_r4(){ $this->_retvalue = $this->cacher->processNocacheCode('<?php /* comment placeholder */?>', $this->compiler,false,false);    }
#line 1524 "internal.templateparser.php"
#line 89 "internal.templateparser.y"
    function yy_r5(){$this->_retvalue = $this->cacher->processNocacheCode($this->yystack[$this->yyidx + -1]->minor, $this->compiler,false,false);    }
#line 1527 "internal.templateparser.php"
#line 91 "internal.templateparser.y"
    function yy_r6(){$this->_retvalue = $this->cacher->processNocacheCode($this->smarty->left_delimiter, $this->compiler,false,false);    }
#line 1530 "internal.templateparser.php"
#line 93 "internal.templateparser.y"
    function yy_r7(){$this->_retvalue = $this->cacher->processNocacheCode($this->smarty->right_delimiter, $this->compiler,false,false);    }
#line 1533 "internal.templateparser.php"
#line 95 "internal.templateparser.y"
    function yy_r8(){if (!$this->template->security || $this->smarty->security_policy->php_handling == SMARTY_PHP_ALLOW) { 
                                      $this->_retvalue = $this->cacher->processNocacheCode($this->yystack[$this->yyidx + 0]->minor, $this->compiler, false,true);
                                      } elseif ($this->smarty->security_policy->php_handling == SMARTY_PHP_QUOTE) {
                                      $this->_retvalue = $this->cacher->processNocacheCode(htmlspecialchars($this->yystack[$this->yyidx + 0]->minor, ENT_QUOTES), $this->compiler, false, false);}    }
#line 1539 "internal.templateparser.php"
#line 100 "internal.templateparser.y"
    function yy_r9(){$this->_retvalue = $this->cacher->processNocacheCode("<?php echo '".$this->yystack[$this->yyidx + 0]->minor."';?>", $this->compiler, false, false);    }
#line 1542 "internal.templateparser.php"
#line 102 "internal.templateparser.y"
    function yy_r10(){$this->_retvalue = $this->cacher->processNocacheCode($this->yystack[$this->yyidx + 0]->minor, $this->compiler,false,false);    }
#line 1545 "internal.templateparser.php"
#line 110 "internal.templateparser.y"
    function yy_r11(){ $this->_retvalue = $this->compiler->compileTag('print_expression',array_merge(array('value'=>$this->yystack[$this->yyidx + -2]->minor),$this->yystack[$this->yyidx + -1]->minor));    }
#line 1548 "internal.templateparser.php"
#line 112 "internal.templateparser.y"
    function yy_r12(){ $this->_retvalue = $this->compiler->compileTag('assign',$this->yystack[$this->yyidx + -1]->minor);    }
#line 1551 "internal.templateparser.php"
#line 114 "internal.templateparser.y"
    function yy_r13(){ $this->_retvalue =  $this->compiler->compileTag($this->yystack[$this->yyidx + -2]->minor,$this->yystack[$this->yyidx + -1]->minor);    }
#line 1554 "internal.templateparser.php"
#line 116 "internal.templateparser.y"
    function yy_r14(){ $this->_retvalue =  $this->compiler->compileTag($this->yystack[$this->yyidx + -4]->minor,array_merge(array('object_methode'=>$this->yystack[$this->yyidx + -2]->minor),$this->yystack[$this->yyidx + -1]->minor));    }
#line 1557 "internal.templateparser.php"
#line 118 "internal.templateparser.y"
    function yy_r15(){ $this->_retvalue =  '<?php ob_start();?>'.$this->compiler->compileTag($this->yystack[$this->yyidx + -4]->minor,$this->yystack[$this->yyidx + -1]->minor).'<?php echo ';
                                                                if ($this->yystack[$this->yyidx + -3]->minor == 'isset' || $this->yystack[$this->yyidx + -3]->minor == 'empty' || is_callable($this->yystack[$this->yyidx + -3]->minor)) {
																					                       if (!$this->template->security || $this->smarty->security_handler->isTrustedModifier($this->yystack[$this->yyidx + -3]->minor, $this->compiler)) {
																					                           $this->_retvalue .= $this->yystack[$this->yyidx + -3]->minor . "(ob_get_clean()". $this->yystack[$this->yyidx + -2]->minor .");?>";
																					                        }
																					                    } else {
																					                       if ($this->smarty->plugin_handler->loadSmartyPlugin($this->yystack[$this->yyidx + -3]->minor,'modifier')) {
                                                                      $this->_retvalue .= "\$_smarty_tpl->smarty->plugin_handler->".$this->yystack[$this->yyidx + -3]->minor . "(array(ob_get_clean()". $this->yystack[$this->yyidx + -2]->minor ."),'modifier');?>";
                                                                 } else {
                                                                      $this->compiler->trigger_template_error ("unknown modifier \"" . $this->yystack[$this->yyidx + -3]->minor . "\"");
                                                                 }
                                                              }
                                                                }
#line 1572 "internal.templateparser.php"
#line 132 "internal.templateparser.y"
    function yy_r16(){ $this->_retvalue =  $this->compiler->compileTag($this->yystack[$this->yyidx + -1]->minor.'close',array());    }
#line 1575 "internal.templateparser.php"
#line 134 "internal.templateparser.y"
    function yy_r17(){ $this->_retvalue =  $this->compiler->compileTag($this->yystack[$this->yyidx + -3]->minor.'close',array('object_methode'=>$this->yystack[$this->yyidx + -1]->minor));    }
#line 1578 "internal.templateparser.php"
#line 136 "internal.templateparser.y"
    function yy_r18(){ $this->_retvalue =  $this->compiler->compileTag($this->yystack[$this->yyidx + -3]->minor,array('ifexp'=>$this->yystack[$this->yyidx + -1]->minor));    }
#line 1581 "internal.templateparser.php"
#line 138 "internal.templateparser.y"
    function yy_r19(){ $this->_retvalue =  $this->compiler->compileTag($this->yystack[$this->yyidx + -9]->minor,array('start'=>$this->yystack[$this->yyidx + -7]->minor,'ifexp'=>$this->yystack[$this->yyidx + -5]->minor,'varloop'=>$this->yystack[$this->yyidx + -2]->minor,'loop'=>$this->yystack[$this->yyidx + -1]->minor));    }
#line 1584 "internal.templateparser.php"
#line 140 "internal.templateparser.y"
    function yy_r20(){ $this->_retvalue =  $this->compiler->compileTag($this->yystack[$this->yyidx + -6]->minor,array('from'=>$this->yystack[$this->yyidx + -1]->minor,'item'=>$this->yystack[$this->yyidx + -3]->minor));    }
#line 1587 "internal.templateparser.php"
#line 142 "internal.templateparser.y"
    function yy_r22(){ $this->_retvalue = '='.$this->yystack[$this->yyidx + 0]->minor;    }
#line 1590 "internal.templateparser.php"
#line 143 "internal.templateparser.y"
    function yy_r23(){ $this->_retvalue = $this->yystack[$this->yyidx + 0]->minor;    }
#line 1593 "internal.templateparser.php"
#line 149 "internal.templateparser.y"
    function yy_r24(){ $this->_retvalue = array_merge($this->yystack[$this->yyidx + -1]->minor,$this->yystack[$this->yyidx + 0]->minor);    }
#line 1596 "internal.templateparser.php"
#line 153 "internal.templateparser.y"
    function yy_r26(){ $this->_retvalue = array();    }
#line 1599 "internal.templateparser.php"
#line 156 "internal.templateparser.y"
    function yy_r27(){ $this->_retvalue = array($this->yystack[$this->yyidx + -2]->minor=>$this->yystack[$this->yyidx + 0]->minor);    }
#line 1602 "internal.templateparser.php"
#line 163 "internal.templateparser.y"
    function yy_r28(){ $this->_retvalue = array($this->yystack[$this->yyidx + 0]->minor);    }
#line 1605 "internal.templateparser.php"
#line 164 "internal.templateparser.y"
    function yy_r29(){ $this->yystack[$this->yyidx + -2]->minor[]=$this->yystack[$this->yyidx + 0]->minor; $this->_retvalue = $this->yystack[$this->yyidx + -2]->minor;    }
#line 1608 "internal.templateparser.php"
#line 166 "internal.templateparser.y"
    function yy_r30(){ $this->_retvalue = array('var' => $this->yystack[$this->yyidx + -2]->minor, 'value'=>$this->yystack[$this->yyidx + 0]->minor);    }
#line 1611 "internal.templateparser.php"
#line 179 "internal.templateparser.y"
    function yy_r34(){ $this->_retvalue = $this->yystack[$this->yyidx + -1]->minor.$this->yystack[$this->yyidx + 0]->minor;     }
#line 1614 "internal.templateparser.php"
#line 181 "internal.templateparser.y"
    function yy_r35(){ $this->_retvalue = $this->yystack[$this->yyidx + -2]->minor . $this->yystack[$this->yyidx + -1]->minor . $this->yystack[$this->yyidx + 0]->minor;     }
#line 1617 "internal.templateparser.php"
#line 183 "internal.templateparser.y"
    function yy_r36(){ $this->_retvalue = $this->yystack[$this->yyidx + -2]->minor . '.' . $this->yystack[$this->yyidx + 0]->minor;     }
#line 1620 "internal.templateparser.php"
#line 196 "internal.templateparser.y"
    function yy_r39(){if ($this->yystack[$this->yyidx + -1]->minor == 'isset' || $this->yystack[$this->yyidx + -1]->minor == 'empty' || is_callable($this->yystack[$this->yyidx + -1]->minor)) {
																					                       if (!$this->template->security || $this->smarty->security_handler->isTrustedModifier($this->yystack[$this->yyidx + -1]->minor, $this->compiler)) {
																					                           $this->_retvalue = $this->yystack[$this->yyidx + -1]->minor . "(". $this->yystack[$this->yyidx + -2]->minor . $this->yystack[$this->yyidx + 0]->minor .")";
																					                        }
																					                    } else {
																					                       if ($this->smarty->plugin_handler->loadSmartyPlugin($this->yystack[$this->yyidx + -1]->minor,'modifier')) {
                                                                      $this->_retvalue = "\$_smarty_tpl->smarty->plugin_handler->".$this->yystack[$this->yyidx + -1]->minor . "(array(". $this->yystack[$this->yyidx + -2]->minor . $this->yystack[$this->yyidx + 0]->minor ."),'modifier')";
                                                                 } else {
                                                                      $this->compiler->trigger_template_error ("unknown modifier\"" . $this->yystack[$this->yyidx + -1]->minor . "\"");
                                                                 }
                                                              }
                                                                }
#line 1634 "internal.templateparser.php"
#line 218 "internal.templateparser.y"
    function yy_r44(){ $this->_retvalue = "'".$this->yystack[$this->yyidx + -1]->minor."'";     }
#line 1637 "internal.templateparser.php"
#line 220 "internal.templateparser.y"
    function yy_r45(){ $this->_retvalue = $this->yystack[$this->yyidx + -3]->minor.'::'.$this->yystack[$this->yyidx + 0]->minor;     }
#line 1640 "internal.templateparser.php"
#line 222 "internal.templateparser.y"
    function yy_r46(){ $this->_retvalue = $this->yystack[$this->yyidx + -3]->minor.'::'.$this->yystack[$this->yyidx + 0]->minor;    }
#line 1643 "internal.templateparser.php"
#line 224 "internal.templateparser.y"
    function yy_r47(){ $this->_retvalue = $this->yystack[$this->yyidx + -5]->minor.'::$'.$this->yystack[$this->yyidx + -1]->minor.$this->yystack[$this->yyidx + 0]->minor;    }
#line 1646 "internal.templateparser.php"
#line 226 "internal.templateparser.y"
    function yy_r48(){ $this->_retvalue = '\''.$this->yystack[$this->yyidx + 0]->minor.'\'';     }
#line 1649 "internal.templateparser.php"
#line 230 "internal.templateparser.y"
    function yy_r50(){ $this->_retvalue = "(". $this->yystack[$this->yyidx + -1]->minor .")";     }
#line 1652 "internal.templateparser.php"
#line 236 "internal.templateparser.y"
    function yy_r51(){ $this->_retvalue = '$_smarty_tpl->getVariable('. $this->yystack[$this->yyidx + -1]->minor .')->value'.$this->yystack[$this->yyidx + 0]->minor; $_var = $this->template->getVariable(trim($this->yystack[$this->yyidx + -1]->minor,"'")); if(!is_null($_var)) if ($_var->nocache) $this->nocache=true;    }
#line 1655 "internal.templateparser.php"
#line 238 "internal.templateparser.y"
    function yy_r52(){ $this->_retvalue = '$_smarty_tpl->getVariable('. $this->yystack[$this->yyidx + -2]->minor .')->'.$this->yystack[$this->yyidx + 0]->minor; $_var = $this->template->getVariable(trim($this->yystack[$this->yyidx + -2]->minor,"'")); if(!is_null($_var)) if ($_var->nocache) $this->nocache=true;    }
#line 1658 "internal.templateparser.php"
#line 240 "internal.templateparser.y"
    function yy_r53(){ $this->_retvalue = '$_'. strtoupper($this->yystack[$this->yyidx + -1]->minor).$this->yystack[$this->yyidx + 0]->minor;    }
#line 1661 "internal.templateparser.php"
#line 248 "internal.templateparser.y"
    function yy_r57(){return;    }
#line 1664 "internal.templateparser.php"
#line 250 "internal.templateparser.y"
    function yy_r58(){ $this->_retvalue = "[". $this->yystack[$this->yyidx + 0]->minor ."]";    }
#line 1667 "internal.templateparser.php"
#line 252 "internal.templateparser.y"
    function yy_r59(){ $this->_retvalue = "[". $this->yystack[$this->yyidx + -1]->minor ."]";    }
#line 1670 "internal.templateparser.php"
#line 258 "internal.templateparser.y"
    function yy_r61(){$this->_retvalue = $this->yystack[$this->yyidx + -1]->minor.'.'.$this->yystack[$this->yyidx + 0]->minor;    }
#line 1673 "internal.templateparser.php"
#line 260 "internal.templateparser.y"
    function yy_r62(){$this->_retvalue = '\''.$this->yystack[$this->yyidx + 0]->minor.'\'';    }
#line 1676 "internal.templateparser.php"
#line 262 "internal.templateparser.y"
    function yy_r63(){$this->_retvalue = '('.$this->yystack[$this->yyidx + -1]->minor.')';    }
#line 1679 "internal.templateparser.php"
#line 267 "internal.templateparser.y"
    function yy_r64(){ $this->_retvalue = '$_smarty_tpl->getVariable('. $this->yystack[$this->yyidx + -2]->minor .')->value'.$this->yystack[$this->yyidx + -1]->minor.$this->yystack[$this->yyidx + 0]->minor; $_var = $this->template->getVariable(trim($this->yystack[$this->yyidx + -2]->minor,"'")); if(!is_null($_var)) if ($_var->nocache) $this->nocache=true;    }
#line 1682 "internal.templateparser.php"
#line 269 "internal.templateparser.y"
    function yy_r65(){$this->_retvalue  = $this->yystack[$this->yyidx + 0]->minor;     }
#line 1685 "internal.templateparser.php"
#line 271 "internal.templateparser.y"
    function yy_r66(){$this->_retvalue  = $this->yystack[$this->yyidx + -1]->minor.$this->yystack[$this->yyidx + 0]->minor;     }
#line 1688 "internal.templateparser.php"
#line 273 "internal.templateparser.y"
    function yy_r67(){ $this->_retvalue = '->'.$this->yystack[$this->yyidx + -1]->minor.$this->yystack[$this->yyidx + 0]->minor;    }
#line 1691 "internal.templateparser.php"
#line 276 "internal.templateparser.y"
    function yy_r68(){ $this->_retvalue = '->'.$this->yystack[$this->yyidx + 0]->minor;    }
#line 1694 "internal.templateparser.php"
#line 281 "internal.templateparser.y"
    function yy_r69(){if (!$this->template->security || $this->smarty->security_handler->isTrustedPhpFunction($this->yystack[$this->yyidx + -3]->minor, $this->compiler)) {
																					            if ($this->yystack[$this->yyidx + -3]->minor == 'isset' || $this->yystack[$this->yyidx + -3]->minor == 'empty' || is_callable($this->yystack[$this->yyidx + -3]->minor)) {
																					                $this->_retvalue = $this->yystack[$this->yyidx + -3]->minor . "(". $this->yystack[$this->yyidx + -1]->minor .")";
																					            } else {
                                                       $this->compiler->trigger_template_error ("unknown fuction\"" . $this->yystack[$this->yyidx + -3]->minor . "\"");
                                                      }
                                                    }    }
#line 1703 "internal.templateparser.php"
#line 292 "internal.templateparser.y"
    function yy_r70(){ $this->_retvalue = $this->yystack[$this->yyidx + -3]->minor . "(". $this->yystack[$this->yyidx + -1]->minor .")";    }
#line 1706 "internal.templateparser.php"
#line 296 "internal.templateparser.y"
    function yy_r71(){ $this->_retvalue = $this->yystack[$this->yyidx + -2]->minor.",".$this->yystack[$this->yyidx + 0]->minor;    }
#line 1709 "internal.templateparser.php"
#line 300 "internal.templateparser.y"
    function yy_r73(){ return;    }
#line 1712 "internal.templateparser.php"
#line 305 "internal.templateparser.y"
    function yy_r74(){ $this->_retvalue =  $this->yystack[$this->yyidx + 0]->minor;    }
#line 1715 "internal.templateparser.php"
#line 311 "internal.templateparser.y"
    function yy_r75(){ $this->_retvalue = $this->yystack[$this->yyidx + -1]->minor.$this->yystack[$this->yyidx + 0]->minor;    }
#line 1718 "internal.templateparser.php"
#line 315 "internal.templateparser.y"
    function yy_r77(){$this->_retvalue = ','.$this->yystack[$this->yyidx + 0]->minor;    }
#line 1721 "internal.templateparser.php"
#line 322 "internal.templateparser.y"
    function yy_r79(){$this->_retvalue = '!'.$this->yystack[$this->yyidx + 0]->minor;    }
#line 1724 "internal.templateparser.php"
#line 327 "internal.templateparser.y"
    function yy_r81(){$this->_retvalue =$this->yystack[$this->yyidx + 0]->minor;    }
#line 1727 "internal.templateparser.php"
#line 328 "internal.templateparser.y"
    function yy_r82(){$this->_retvalue = $this->yystack[$this->yyidx + -2]->minor.$this->yystack[$this->yyidx + -1]->minor.$this->yystack[$this->yyidx + 0]->minor;    }
#line 1730 "internal.templateparser.php"
#line 331 "internal.templateparser.y"
    function yy_r84(){$this->_retvalue = '==';    }
#line 1733 "internal.templateparser.php"
#line 332 "internal.templateparser.y"
    function yy_r85(){$this->_retvalue = '!=';    }
#line 1736 "internal.templateparser.php"
#line 333 "internal.templateparser.y"
    function yy_r86(){$this->_retvalue = '>';    }
#line 1739 "internal.templateparser.php"
#line 334 "internal.templateparser.y"
    function yy_r87(){$this->_retvalue = '<';    }
#line 1742 "internal.templateparser.php"
#line 335 "internal.templateparser.y"
    function yy_r88(){$this->_retvalue = '>=';    }
#line 1745 "internal.templateparser.php"
#line 336 "internal.templateparser.y"
    function yy_r89(){$this->_retvalue = '<=';    }
#line 1748 "internal.templateparser.php"
#line 337 "internal.templateparser.y"
    function yy_r90(){$this->_retvalue = '===';    }
#line 1751 "internal.templateparser.php"
#line 338 "internal.templateparser.y"
    function yy_r91(){$this->_retvalue = '!==';    }
#line 1754 "internal.templateparser.php"
#line 340 "internal.templateparser.y"
    function yy_r92(){$this->_retvalue = '&&';    }
#line 1757 "internal.templateparser.php"
#line 341 "internal.templateparser.y"
    function yy_r93(){$this->_retvalue = '||';    }
#line 1760 "internal.templateparser.php"
#line 343 "internal.templateparser.y"
    function yy_r94(){ $this->_retvalue = 'array('.$this->yystack[$this->yyidx + -1]->minor.')';    }
#line 1763 "internal.templateparser.php"
#line 345 "internal.templateparser.y"
    function yy_r96(){ $this->_retvalue = $this->yystack[$this->yyidx + -2]->minor.','.$this->yystack[$this->yyidx + 0]->minor;     }
#line 1766 "internal.templateparser.php"
#line 346 "internal.templateparser.y"
    function yy_r97(){ return;     }
#line 1769 "internal.templateparser.php"
#line 348 "internal.templateparser.y"
    function yy_r99(){ $this->_retvalue = $this->yystack[$this->yyidx + -2]->minor.'=>'.$this->yystack[$this->yyidx + 0]->minor;    }
#line 1772 "internal.templateparser.php"
#line 353 "internal.templateparser.y"
    function yy_r103(){$this->_retvalue = "'.".$this->yystack[$this->yyidx + 0]->minor.".'";    }
#line 1775 "internal.templateparser.php"
#line 354 "internal.templateparser.y"
    function yy_r104(){$this->_retvalue = "'.".$this->yystack[$this->yyidx + -1]->minor.".'";    }
#line 1778 "internal.templateparser.php"
#line 355 "internal.templateparser.y"
    function yy_r105(){$this->_retvalue = "'.(".$this->yystack[$this->yyidx + -1]->minor.").'";    }
#line 1781 "internal.templateparser.php"
#line 356 "internal.templateparser.y"
    function yy_r106(){$this->_retvalue = addslashes($this->yystack[$this->yyidx + 0]->minor);    }
#line 1784 "internal.templateparser.php"

    /**
     * placeholder for the left hand side in a reduce operation.
     * 
     * For a parser with a rule like this:
     * <pre>
     * rule(A) ::= B. { A = 1; }
     * </pre>
     * 
     * The parser will translate to something like:
     * 
     * <code>
     * function yy_r0(){$this->_retvalue = 1;}
     * </code>
     */
    private $_retvalue;

    /**
     * Perform a reduce action and the shift that must immediately
     * follow the reduce.
     * 
     * For a rule such as:
     * 
     * <pre>
     * A ::= B blah C. { dosomething(); }
     * </pre>
     * 
     * This function will first call the action, if any, ("dosomething();" in our
     * example), and then it will pop three states from the stack,
     * one for each entry on the right-hand side of the expression
     * (B, blah, and C in our example rule), and then push the result of the action
     * back on to the stack with the resulting state reduced to (as described in the .out
     * file)
     * @param int Number of the rule by which to reduce
     */
    function yy_reduce($yyruleno)
    {
        //int $yygoto;                     /* The next state */
        //int $yyact;                      /* The next action */
        //mixed $yygotominor;        /* The LHS of the rule reduced */
        //TP_yyStackEntry $yymsp;            /* The top of the parser's stack */
        //int $yysize;                     /* Amount to pop the stack */
        $yymsp = $this->yystack[$this->yyidx];
        if (self::$yyTraceFILE && $yyruleno >= 0 
              && $yyruleno < count(self::$yyRuleName)) {
            fprintf(self::$yyTraceFILE, "%sReduce (%d) [%s].\n",
                self::$yyTracePrompt, $yyruleno,
                self::$yyRuleName[$yyruleno]);
        }

        $this->_retvalue = $yy_lefthand_side = null;
        if (array_key_exists($yyruleno, self::$yyReduceMap)) {
            // call the action
            $this->_retvalue = null;
            $this->{'yy_r' . self::$yyReduceMap[$yyruleno]}();
            $yy_lefthand_side = $this->_retvalue;
        }
        $yygoto = self::$yyRuleInfo[$yyruleno]['lhs'];
        $yysize = self::$yyRuleInfo[$yyruleno]['rhs'];
        $this->yyidx -= $yysize;
        for($i = $yysize; $i; $i--) {
            // pop all of the right-hand side parameters
            array_pop($this->yystack);
        }
        $yyact = $this->yy_find_reduce_action($this->yystack[$this->yyidx]->stateno, $yygoto);
        if ($yyact < self::YYNSTATE) {
            /* If we are not debugging and the reduce action popped at least
            ** one element off the stack, then we can push the new element back
            ** onto the stack here, and skip the stack overflow test in yy_shift().
            ** That gives a significant speed improvement. */
            if (!self::$yyTraceFILE && $yysize) {
                $this->yyidx++;
                $x = new TP_yyStackEntry;
                $x->stateno = $yyact;
                $x->major = $yygoto;
                $x->minor = $yy_lefthand_side;
                $this->yystack[$this->yyidx] = $x;
            } else {
                $this->yy_shift($yyact, $yygoto, $yy_lefthand_side);
            }
        } elseif ($yyact == self::YYNSTATE + self::YYNRULE + 1) {
            $this->yy_accept();
        }
    }

    /**
     * The following code executes when the parse fails
     * 
     * Code from %parse_fail is inserted here
     */
    function yy_parse_failed()
    {
        if (self::$yyTraceFILE) {
            fprintf(self::$yyTraceFILE, "%sFail!\n", self::$yyTracePrompt);
        }
        while ($this->yyidx >= 0) {
            $this->yy_pop_parser_stack();
        }
        /* Here code is inserted which will be executed whenever the
        ** parser fails */
    }

    /**
     * The following code executes when a syntax error first occurs.
     * 
     * %syntax_error code is inserted here
     * @param int The major type of the error token
     * @param mixed The minor type of the error token
     */
    function yy_syntax_error($yymajor, $TOKEN)
    {
#line 53 "internal.templateparser.y"

    $this->internalError = true;
    $this->compiler->trigger_template_error();
#line 1901 "internal.templateparser.php"
    }

    /**
     * The following is executed when the parser accepts
     * 
     * %parse_accept code is inserted here
     */
    function yy_accept()
    {
        if (self::$yyTraceFILE) {
            fprintf(self::$yyTraceFILE, "%sAccept!\n", self::$yyTracePrompt);
        }
        while ($this->yyidx >= 0) {
            $stack = $this->yy_pop_parser_stack();
        }
        /* Here code is inserted which will be executed whenever the
        ** parser accepts */
#line 45 "internal.templateparser.y"

    $this->successful = !$this->internalError;
    $this->internalError = false;
    $this->retvalue = $this->_retvalue;
    //echo $this->retvalue."\n\n";
#line 1926 "internal.templateparser.php"
    }

    /**
     * The main parser program.
     * 
     * The first argument is the major token number.  The second is
     * the token value string as scanned from the input.
     *
     * @param int the token number
     * @param mixed the token value
     * @param mixed any extra arguments that should be passed to handlers
     */
    function doParse($yymajor, $yytokenvalue)
    {
//        $yyact;            /* The parser action. */
//        $yyendofinput;     /* True if we are at the end of input */
        $yyerrorhit = 0;   /* True if yymajor has invoked an error */
        
        /* (re)initialize the parser, if necessary */
        if ($this->yyidx === null || $this->yyidx < 0) {
            /* if ($yymajor == 0) return; // not sure why this was here... */
            $this->yyidx = 0;
            $this->yyerrcnt = -1;
            $x = new TP_yyStackEntry;
            $x->stateno = 0;
            $x->major = 0;
            $this->yystack = array();
            array_push($this->yystack, $x);
        }
        $yyendofinput = ($yymajor==0);
        
        if (self::$yyTraceFILE) {
            fprintf(self::$yyTraceFILE, "%sInput %s\n",
                self::$yyTracePrompt, $this->yyTokenName[$yymajor]);
        }
        
        do {
            $yyact = $this->yy_find_shift_action($yymajor);
            if ($yymajor < self::YYERRORSYMBOL &&
                  !$this->yy_is_expected_token($yymajor)) {
                // force a syntax error
                $yyact = self::YY_ERROR_ACTION;
            }
            if ($yyact < self::YYNSTATE) {
                $this->yy_shift($yyact, $yymajor, $yytokenvalue);
                $this->yyerrcnt--;
                if ($yyendofinput && $this->yyidx >= 0) {
                    $yymajor = 0;
                } else {
                    $yymajor = self::YYNOCODE;
                }
            } elseif ($yyact < self::YYNSTATE + self::YYNRULE) {
                $this->yy_reduce($yyact - self::YYNSTATE);
            } elseif ($yyact == self::YY_ERROR_ACTION) {
                if (self::$yyTraceFILE) {
                    fprintf(self::$yyTraceFILE, "%sSyntax Error!\n",
                        self::$yyTracePrompt);
                }
                if (self::YYERRORSYMBOL) {
                    /* A syntax error has occurred.
                    ** The response to an error depends upon whether or not the
                    ** grammar defines an error token "ERROR".  
                    **
                    ** This is what we do if the grammar does define ERROR:
                    **
                    **  * Call the %syntax_error function.
                    **
                    **  * Begin popping the stack until we enter a state where
                    **    it is legal to shift the error symbol, then shift
                    **    the error symbol.
                    **
                    **  * Set the error count to three.
                    **
                    **  * Begin accepting and shifting new tokens.  No new error
                    **    processing will occur until three tokens have been
                    **    shifted successfully.
                    **
                    */
                    if ($this->yyerrcnt < 0) {
                        $this->yy_syntax_error($yymajor, $yytokenvalue);
                    }
                    $yymx = $this->yystack[$this->yyidx]->major;
                    if ($yymx == self::YYERRORSYMBOL || $yyerrorhit ){
                        if (self::$yyTraceFILE) {
                            fprintf(self::$yyTraceFILE, "%sDiscard input token %s\n",
                                self::$yyTracePrompt, $this->yyTokenName[$yymajor]);
                        }
                        $this->yy_destructor($yymajor, $yytokenvalue);
                        $yymajor = self::YYNOCODE;
                    } else {
                        while ($this->yyidx >= 0 &&
                                 $yymx != self::YYERRORSYMBOL &&
        ($yyact = $this->yy_find_shift_action(self::YYERRORSYMBOL)) >= self::YYNSTATE
                              ){
                            $this->yy_pop_parser_stack();
                        }
                        if ($this->yyidx < 0 || $yymajor==0) {
                            $this->yy_destructor($yymajor, $yytokenvalue);
                            $this->yy_parse_failed();
                            $yymajor = self::YYNOCODE;
                        } elseif ($yymx != self::YYERRORSYMBOL) {
                            $u2 = 0;
                            $this->yy_shift($yyact, self::YYERRORSYMBOL, $u2);
                        }
                    }
                    $this->yyerrcnt = 3;
                    $yyerrorhit = 1;
                } else {
                    /* YYERRORSYMBOL is not defined */
                    /* This is what we do if the grammar does not define ERROR:
                    **
                    **  * Report an error message, and throw away the input token.
                    **
                    **  * If the input token is $, then fail the parse.
                    **
                    ** As before, subsequent error messages are suppressed until
                    ** three input tokens have been successfully shifted.
                    */
                    if ($this->yyerrcnt <= 0) {
                        $this->yy_syntax_error($yymajor, $yytokenvalue);
                    }
                    $this->yyerrcnt = 3;
                    $this->yy_destructor($yymajor, $yytokenvalue);
                    if ($yyendofinput) {
                        $this->yy_parse_failed();
                    }
                    $yymajor = self::YYNOCODE;
                }
            } else {
                $this->yy_accept();
                $yymajor = self::YYNOCODE;
            }            
        } while ($yymajor != self::YYNOCODE && $this->yyidx >= 0);
    }
}
