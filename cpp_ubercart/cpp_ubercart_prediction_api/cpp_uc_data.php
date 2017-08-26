<?php
/**
 * This class provides all the CPP api implementations to predict if a user comment about a purchased order is a
 * good or bad review based on the implementation of artificial intelligence algorithms,
 *
 * The development and maintenance of this module is made by ThinkBinario: thinkbinario.com
 * @package    Customer Product Predictor
 * @author     Jose Ortiz <jose@thinkbinario.com>
 */

class CPP_UC_Data_Manager
{
    /* Private properties */
    var $_type;
    var $_reviews_data;
    var $_bad_reviews;
    var $_good_reviews;
    var $_total_reviews;
    var $_order;
    var $_number_unique_words;

    /**
     * CPP_UC_Data_Manager constructor.
     * @param null $type : optional type. 1 means only administrator can see the comments,
     *                     null or not set means that everyone can see the comments.
     */
    public function __construct($type = null)
    {
        if ($type != null)
            $this->_type = $type;
    }

    /**
     * 'Overloads' default constructor to load a new order so its comment can be predicted
     *  @param $order_id
     *  @param null $type : optional type. 1 means only administrator can see the comments,
     *                     null or not set means that everyone can see the comments.
     * @return CPP_UC_Data_Manager instance of this class.
     */
    public static function loadNewOrder($order_id, $type = null)
    {
        $instance = new self($type);
        $instance->_order = cpp_ubercart_load_data_by_order($order_id);
        $instance->_number_unique_words = array();
        $instance->cpp_uc_data_manager_load_review_predictions();
        $instance->cpp_uc_data_manager_set_order_review_prediction();
        return $instance;
    }

    //-----------------------------------------------------------------------------------------------------------------
    //                                        Public methods
    //-----------------------------------------------------------------------------------------------------------------

    /**
     * @return mixed the data containing the prediction for this order comment
     *     'comment_id' =>   The comment id of the order.
     *     'order_id' =>     The order id.
     *     'uid' =>          The user id of the user who purchased the order ( if 0, then the user had anonymous role )
     *     'comment' =>      The comment about this order created by the user at purchase time.
     *                       Note that this comment is set to be seen by everyone. There are comments which are set to be
     *                       only seen by the administrator. In future versions of CPP we'll add predictions for this feature.
     *     'prediction' =>   the prediction about the comment left by the user. Options are: ood review or bad review
     *                       which determined by implementing artificial intelligence algorithms.
     */
    public function cpp_uc_get_product_review_data()
    {
        return $this->_order;
    }

    //-----------------------------------------------------------------------------------------------------------------
    //                                        Private methods
    //-----------------------------------------------------------------------------------------------------------------

    /**
     * Sets new keys in $this->_order property after order comment is predicted
     */
    private function cpp_uc_data_manager_set_order_review_prediction()
    {
        $good_review_prob = $this->cpp_uc_data_manager_w_in_good();
        $bad_review_prob = $this->cpp_uc_data_manager_w_in_bad();
        $prediction = (($good_review_prob >= $bad_review_prob) ? 'good review' : 'bad review');
        $this->_order['prediction'] = $prediction;
        if ($this->_order['comment_id'] && !cpp_ubercart_comment_exist($this->_order['comment_id']) &&
            $this->_order['review'] != "")
            cpp_ubercart_save_product_review_prediction($this->_order['comment_id'], $prediction);
    }

    /**
     *  Loads all the orders predictions from the system
     */
    private function cpp_uc_data_manager_load_review_predictions()
    {
        $this->_total_reviews = 0;
        $this->_bad_reviews = array();
        $this->_good_reviews = array();
        $reviews_data = $this->cpp_uc_data_manager_product_review_predictions ();
        foreach ($reviews_data as $data)
        {
            $old_order = cpp_ubercart_load_data_by_comment_id($data['comment_id']);
            $old_order_message = $old_order['review'];
            if ($data['prediction'] == PredictionsDefinitions::BAD_REVIEW)
                $this->_bad_reviews[$data['prid']] = array('comment_id' => $data['comment_id'],
                                          'prediction' => PredictionsDefinitions::BAD_REVIEW,
                                          'review' => $old_order_message);
            elseif ($data['prediction'] == PredictionsDefinitions::GOOD_REVIEW)
                $this->_good_reviews[$data['prid']] = array('comment_id' => $data['comment_id'],
                                     'prediction' => PredictionsDefinitions::GOOD_REVIEW,
                                     'review' => $old_order_message);
        }
        $this->_total_reviews = sizeof($this->_bad_reviews) + sizeof($this->_good_reviews);
        if (!$this->_total_reviews)
            $this->_total_reviews++;
    }

    /**
     * @return array : the existing order comments that were predicted as bad reviews from all the purchases.
     */
    private function cpp_uc_data_manager_scan_bad_messages()
    {
        $message_scanned = array();
        $words_in_bad = 0;
        $new_message = $this->cpp_uc_data_manager_tokenize_message($this->_order['review']);
        foreach ($new_message as $new_word)
        {
            if (!isset($message_scanned[$new_word]))
                $message_scanned[$new_word] = 1;
            if (!in_array($new_word, $this->_number_unique_words))
                $this->_number_unique_words[] = $new_word;
            foreach ($this->_bad_reviews as $review)
            {
                $old_message = $review['review'];
                $message = $this->cpp_uc_data_manager_tokenize_message($old_message);
                $words_in_bad += sizeof($message);
                foreach ($message as $old_word)
                {
                    if (!in_array($old_word, $this->_number_unique_words))
                        $this->_number_unique_words[] = $old_word;
                    if ($new_word == $old_word)
                        $message_scanned[$new_word] += 1;
                }
            }
        }

        foreach ($message_scanned as $new_word => $number_found)
        {
            if ($number_found > 1)
                $message_scanned[$new_word]++;
        }
        $message_scanned['words_in_bad'] = $words_in_bad;
        return $message_scanned;
    }

    /**
     * @return array : the existing order comments that were predicted as good reviews from all the purchases.
     */
    private function cpp_uc_data_manager_scan_good_messages()
    {
        $message_scanned = array();
        $words_in_good = 0;
        $new_message = $this->cpp_uc_data_manager_tokenize_message($this->_order['review']);
        foreach ($new_message as $new_word)
        {
            if (!isset($message_scanned[$new_word]))
                $message_scanned[$new_word] = 1;
            if (!in_array($new_word, $this->_number_unique_words))
                $this->_number_unique_words[] = $new_word;
            foreach ($this->_good_reviews as $review)
            {
                $old_message = $review['review'];
                $message = $this->cpp_uc_data_manager_tokenize_message($old_message);
                $words_in_good += sizeof($message);
                foreach ($message as $old_word)
                {
                    if (!in_array($old_word, $this->_number_unique_words))
                        $this->_number_unique_words[] = $old_word;
                    if ($new_word == $old_word)
                        $message_scanned[$new_word] += 1;
                }
            }
        }

        foreach ($message_scanned as $new_word => $number_found)
        {
            if ($number_found > 1)
                $message_scanned[$new_word]++;
        }
        $message_scanned['words_in_good'] = $words_in_good;
        return $message_scanned;
    }

    /**
     *
     * @return array : all the CPP product review predictions
     */
    function cpp_uc_data_manager_product_review_predictions ()
    {

        $predictions = db_select('cpp_uc_products_review_prediction', 'cprp')->fields('cprp')->execute()->fetchAll(PDO::FETCH_ASSOC);
        return $predictions;

    }

    /**
     * @return int : the number of bad reviews from existing orders
     */
    private function cpp_uc_data_manager_get_number_bad_reviews ()
    {
        return sizeof($this->_bad_reviews);
    }

    /**
     * @return int : the number of good reviews from existing orders
     */
    private function cpp_uc_data_manager_get_number_good_reviews ()
    {
        return sizeof($this->_good_reviews);
    }

    /**
     * @param $message
     * @return array|mixed|string the tokenized message
     */
    private function cpp_uc_data_manager_tokenize_message($message)
    {
        $review = strtolower($message);
        $review = preg_replace("/[^A-Za-z ]/", '', $review);
        $review = explode(" ", $review); // split string by space delimeter
        return $review;
    }

    /**
     * @return float|int : the probability that the new comment is a good review
     */
    private function cpp_uc_data_manager_p_good()
    {
        return ($this->cpp_uc_data_manager_get_number_good_reviews() / $this->_total_reviews);
    }

    /**
     * @return float|int the probability that the new comment is a bad review
     */
    private function cpp_uc_data_manager_p_bad()
    {
        return ($this->cpp_uc_data_manager_get_number_bad_reviews() / $this->_total_reviews);
    }

    /**
     * @return float|int : the probability that the new comment is a bad review based on the total comments who have
     *                     negative reviews
     */
    private function cpp_uc_data_manager_w_in_bad()
    {
        $prob_word_in_bad = 1;
        $words_in_bad = $this->cpp_uc_data_manager_scan_bad_messages();
        $words_in_bad['words_in_bad'] += sizeof($this->_number_unique_words);
        foreach ($words_in_bad as $words)
        {
            $prob_word_in_bad *= $words/$words_in_bad['words_in_bad'];
        }
        $prob_word_in_bad *= $this->cpp_uc_data_manager_p_bad();
        return $prob_word_in_bad;
    }

    /**
     * @return float|int : the probability that the new comment is a good review based on the total comments who have
     *                     positive reviews
     */
    private function cpp_uc_data_manager_w_in_good()
    {
        $prob_word_in_good = 1;
        $words_in_good = $this->cpp_uc_data_manager_scan_good_messages();
        $words_in_good['words_in_good'] += sizeof($this->_number_unique_words);
        foreach ($words_in_good as $words)
        {
            $prob_word_in_good *= $words/$words_in_good['words_in_good'];
        }
        $prob_word_in_good *= $this->cpp_uc_data_manager_p_good();
        return $prob_word_in_good;
    }
}