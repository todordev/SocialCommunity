<?php
/**
 * @package      SocialCommunity
 * @subpackage   Components
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2016 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

// no direct access
defined('_JEXEC') or die;

/**
 * SocialCommunity export controller
 *
 * @package      SocialCommunity
 * @subpackage   Components
 */
class SocialCommunityControllerExport extends JControllerLegacy
{
    public function getModel($name = 'Export', $prefix = 'SocialCommunityModel', $config = array('ignore_request' => true))
    {
        $model = parent::getModel($name, $prefix, $config);
        return $model;
    }

    public function download()
    {
        $app = JFactory::getApplication();
        /** @var $app JApplicationAdministrator */

        $type  = $this->input->get->getCmd('type');
        $model = $this->getModel();

        try {

            switch ($type) {
                case 'locations':
                    $output   = $model->getLocations();
                    $fileName = 'locations.xml';
                    break;

                case 'countries':
                    $output   = $model->getCountries();
                    $fileName = 'countries.xml';
                    break;

                case 'states':
                    $output   = $model->getStates();
                    $fileName = 'states.xml';
                    break;

                default: // Error
                    $output   = '';
                    $fileName = 'error.xml';
                    break;
            }

        } catch (Exception $e) {
            JLog::add($e->getMessage());
            throw new Exception(JText::_('COM_SOCIALCOMMUNITY_ERROR_SYSTEM'));
        }

        jimport('joomla.filesystem.folder');
        jimport('joomla.filesystem.file');
        jimport('joomla.filesystem.path');
        jimport('joomla.filesystem.archive');

        $tmpFolder = JPath::clean($app->get('tmp_path'));

        $date = new JDate();
        $date = $date->format('d_m_Y_H_i_s');

        $archiveName = JFile::stripExt(basename($fileName)) . '_' . $date;
        $archiveFile = $archiveName . '.zip';
        $destination = $tmpFolder . DIRECTORY_SEPARATOR . $archiveFile;

        // compression type
        $zipAdapter   = JArchive::getAdapter('zip');
        $filesToZip[] = array(
            'name' => $fileName,
            'data' => $output
        );

        $zipAdapter->create($destination, $filesToZip, array());

        $filesize = filesize($destination);

        JFactory::getApplication()->setHeader('Content-Type', 'application/octet-stream', true);
        JFactory::getApplication()->setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0', true);
        JFactory::getApplication()->setHeader('Content-Transfer-Encoding', 'binary', true);
        JFactory::getApplication()->setHeader('Pragma', 'no-cache', true);
        JFactory::getApplication()->setHeader('Expires', '0', true);
        JFactory::getApplication()->setHeader('Content-Disposition', 'attachment; filename=' . $archiveFile, true);
        JFactory::getApplication()->setHeader('Content-Length', $filesize, true);

        $doc = JFactory::getDocument();
        $doc->setMimeEncoding('application/octet-stream');

        $app->sendHeaders();

        echo file_get_contents($destination);
        $app->close();
    }
}
