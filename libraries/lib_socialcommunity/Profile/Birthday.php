<?php
/**
 * @package      Socialcommunity
 * @subpackage   Profile
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2017 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Socialcommunity\Profile;

/**
 * This is a value object of the birthday.
 *
 * @package      Socialcommunity
 * @subpackage   Profile
 */
class Birthday
{
    /**
     * @var int
     */
    protected $day;
    protected $month;
    protected $year;

    public function __construct(\DateTime $date = null)
    {
        if ($date !== null) {
            $this->day   = (int)$date->format('d');
            $this->month = (int)$date->format('m');
            $this->year  = (int)$date->format('Y');
        }
    }

    /**
     * Return the day of the birthday.
     *
     * @return int
     */
    public function getDay()
    {
        return (int)$this->day;
    }

    /**
     * Return the month of the birthday.
     *
     * @return int
     */
    public function getMonth()
    {
        return (int)$this->month;
    }

    /**
     * Return the year of the birthday.
     *
     * @return int
     */
    public function getYear()
    {
        return (int)$this->year;
    }

    /**
     * @param int $day
     *
     * @throws \InvalidArgumentException
     */
    public function setDay($day)
    {
        if ($day > 31) {
            throw new \InvalidArgumentException('The day cannot be greater than 31.');
        }

        $this->day = (int)$day;
    }

    /**
     * @param int $month
     *
     * @throws \InvalidArgumentException
     */
    public function setMonth($month)
    {
        if ($month > 12) {
            throw new \InvalidArgumentException('The month cannot be greater than 12.');
        }

        $this->month = (int)$month;
    }

    /**
     * @param int $year
     *
     * @throws \InvalidArgumentException
     */
    public function setYear($year)
    {
        if ($year < 1000 or $year > 2100) {
            throw new \InvalidArgumentException('The year cannot be less than 1000 and greater than 2100.');
        }

        $this->year = (int)$year;
    }

    public function __toString()
    {
        return $this->year.'-'.$this->month.'-'.$this->day;
    }
}
